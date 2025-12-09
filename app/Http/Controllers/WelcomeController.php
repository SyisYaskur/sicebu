<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\RefClass;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. DATA KELAS & LEADERBOARD ---
        $classes = RefClass::withSum('incomes', 'amount')
                           ->withSum('expenses', 'amount')
                           ->orderBy('academic_level')
                           ->orderBy('name')
                           ->get();

        // Hitung saldo masing-masing kelas
        $classes->transform(function($cls) {
            $cls->current_balance = $cls->incomes_sum_amount - $cls->expenses_sum_amount;
            return $cls;
        });

        // Top 3 & Bottom 3
        $top3Classes = $classes->sortByDesc('current_balance')->take(3);
        $low3Classes = $classes->sortBy('current_balance')->take(3);

        // --- 2. FILTER DATA UTAMA ---
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');

        // --- 3. HITUNG SALDO SEKOLAH (DINAMIS) ---
        $qIncome = SClassIncome::query();
        $qExpense = SClassExpense::query();

        if ($classId) {
            $qIncome->where('class_id', $classId);
            $qExpense->where('class_id', $classId);
        }

        $globalIncome = (clone $qIncome)->where('date', '<=', $endDate)->sum('amount');
        $globalExpense = (clone $qExpense)->where('expense_date', '<=', $endDate)->sum('amount');
        $globalBalance = $globalIncome - $globalExpense;

        // --- 4. QUERY TABEL TRANSAKSI (SAMA SEPERTI SEBELUMNYA) ---
        $incomesQuery = SClassIncome::query()
            ->join('ref_classes', 's_class_incomes.class_id', '=', 'ref_classes.id')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('s_class_incomes.date', 's_class_incomes.amount', 's_class_incomes.description', 's_class_incomes.created_at', 'ref_classes.name as class_name_raw', 'ref_classes.academic_level', DB::raw("'income' as type"), DB::raw("NULL as recipient"));

        $expensesQuery = SClassExpense::query()
            ->join('ref_classes', 's_class_expenses.class_id', '=', 'ref_classes.id')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('s_class_expenses.expense_date as date', 's_class_expenses.amount', 's_class_expenses.description', 's_class_expenses.created_at', 'ref_classes.name as class_name_raw', 'ref_classes.academic_level', DB::raw("'expense' as type"), 's_class_expenses.recipient');

        if ($classId) {
            $incomesQuery->where('s_class_incomes.class_id', $classId);
            $expensesQuery->where('s_class_expenses.class_id', $classId);
            $prevInc = SClassIncome::where('class_id', $classId)->where('date', '<', $startDate)->sum('amount');
            $prevExp = SClassExpense::where('class_id', $classId)->where('expense_date', '<', $startDate)->sum('amount');
            $runningBalance = $prevInc - $prevExp;
        } else {
            $prevInc = SClassIncome::where('date', '<', $startDate)->sum('amount');
            $prevExp = SClassExpense::where('expense_date', '<', $startDate)->sum('amount');
            $runningBalance = $prevInc - $prevExp;
        }

        $transactions = $incomesQuery->union($expensesQuery)->orderBy('date', 'asc')->orderBy('created_at', 'asc')->get();

        $processedData = collect([]);
        $processedData->push((object)[
            'date' => Carbon::parse($startDate)->subDay(),
            'class_full_name' => '-',
            'description' => 'Saldo Awal (Sebelum Periode)',
            'income' => 0, 'expense' => 0,
            'balance' => $runningBalance,
            'is_header' => true
        ]);

        foreach ($transactions as $t) {
            $className = $t->academic_level . ' ' . $t->class_name_raw;
            if ($t->type == 'income') {
                $runningBalance += $t->amount; $inc = $t->amount; $exp = 0;
            } else {
                $runningBalance -= $t->amount; $inc = 0; $exp = $t->amount;
            }
            $processedData->push((object)[
                'date' => Carbon::parse($t->date),
                'class_full_name' => $className,
                'description' => $t->description . ($t->recipient ? ' [Ke: '.$t->recipient.']' : ''),
                'income' => $inc, 'expense' => $exp,
                'balance' => $runningBalance,
                'is_header' => false
            ]);
        }

        $displayData = $processedData->reverse()->values();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $displayData->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedData = new LengthAwarePaginator($currentItems, $displayData->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);
        $paginatedData->useBootstrapFive();

        // --- 5. DATA GRAFIK AWAL (7 HARI TERAKHIR) ---
        $chartDates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $chartDates->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $chartData = [
            'categories' => $chartDates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'incomes' => [],
            'expenses' => []
        ];

        foreach ($chartDates as $d) {
            $chartData['incomes'][] = SClassIncome::whereDate('date', $d)->sum('amount');
            $chartData['expenses'][] = SClassExpense::whereDate('expense_date', $d)->sum('amount');
        }

        return view('welcome', compact(
            'classes', 'top3Classes', 'low3Classes', 'globalBalance',
            'paginatedData', 'startDate', 'endDate', 'classId', 'chartData'
        ));
    }

    // API Endpoint untuk Grafik (WAJIB ADA)
    public function chartData(Request $request)
    {
        $range = $request->query('range', '7days');
        $endDate = Carbon::today();
        
        if ($range == '30days') $startDate = Carbon::today()->subDays(29);
        elseif ($range == '1year') $startDate = Carbon::today()->subMonths(11)->startOfMonth();
        else $startDate = Carbon::today()->subDays(6);

        $data = [ 'categories' => [], 'incomes' => [], 'expenses' => [] ];

        if ($range == '1year') {
            for ($i = 0; $i <= 11; $i++) {
                $d = (clone $startDate)->addMonths($i);
                $data['categories'][] = $d->format('M Y');
                $data['incomes'][] = SClassIncome::whereYear('date', $d->year)->whereMonth('date', $d->month)->sum('amount');
                $data['expenses'][] = SClassExpense::whereYear('expense_date', $d->year)->whereMonth('expense_date', $d->month)->sum('amount');
            }
        } else {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $d) {
                $dateStr = $d->format('Y-m-d');
                $data['categories'][] = $d->format('d M');
                $data['incomes'][] = SClassIncome::whereDate('date', $dateStr)->sum('amount');
                $data['expenses'][] = SClassExpense::whereDate('expense_date', $dateStr)->sum('amount');
            }
        }
        return response()->json($data);
    }
}