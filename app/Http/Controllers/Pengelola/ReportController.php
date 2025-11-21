<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RefClass;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = RefClass::orderBy('academic_level')->orderBy('name')->get();
        
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $selectedClassId = $request->input('class_id');
        $filterCategory = $request->input('filter_category');

        // --- MODE 1: MIKRO (Rekapitulasi Harian Kelas) ---
        if ($selectedClassId) {
            $class = RefClass::find($selectedClassId);
            
            // 1. Hitung Saldo Awal (Saldo sebelum tanggal filter)
            $prevIncome = SClassIncome::where('class_id', $selectedClassId)
                ->where('date', '<', $startDate)
                ->sum('amount');
            $prevExpense = SClassExpense::where('class_id', $selectedClassId)
                ->where('expense_date', '<', $startDate)
                ->sum('amount');
            $openingBalance = $prevIncome - $prevExpense;

            // 2. Ambil Data Transaksi (Group by Date di Database)
            $incomes = SClassIncome::where('class_id', $selectedClassId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('DATE(date) as day_date, sum(amount) as total_income')
                ->groupBy('day_date')
                ->get()
                ->keyBy('day_date'); // Key menggunakan string tanggal 'YYYY-MM-DD'

            $expenses = SClassExpense::where('class_id', $selectedClassId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->selectRaw('DATE(expense_date) as day_date, sum(amount) as total_expense')
                ->groupBy('day_date')
                ->get()
                ->keyBy('day_date');

            // 3. Gabungkan Tanggal (Union Keys) & Urutkan ASC untuk perhitungan saldo
            $allDates = $incomes->keys()->merge($expenses->keys())->unique()->sort();

            // 4. Proses Hitung Saldo (Kronologis: Lama -> Baru)
            $runningBalance = $openingBalance;
            $totalPeriodIncome = 0;
            $totalPeriodExpense = 0;
            $processedData = collect([]);

            foreach ($allDates as $dateStr) {
                $inc = isset($incomes[$dateStr]) ? $incomes[$dateStr]->total_income : 0;
                $exp = isset($expenses[$dateStr]) ? $expenses[$dateStr]->total_expense : 0;

                $runningBalance += ($inc - $exp);
                $totalPeriodIncome += $inc;
                $totalPeriodExpense += $exp;

                $processedData->push((object) [
                    'date' => Carbon::parse($dateStr),
                    'income' => $inc,
                    'expense' => $exp,
                    'balance' => $runningBalance,
                ]);
            }

            // 5. Balik Urutan untuk Tampilan (Terbaru di Atas)
            $displayData = $processedData->sortByDesc('date')->values();

            // 6. Manual Pagination
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $displayData->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $paginatedData = new LengthAwarePaginator($currentItems, $displayData->count(), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]);
            $paginatedData->useBootstrapFive();

            $stats = [
                'openingBalance' => $openingBalance,
                'totalIncome' => $totalPeriodIncome,
                'totalExpense' => $totalPeriodExpense,
                'finalBalance' => $runningBalance,
            ];
            
            return view('pengelola.reports.index', compact(
                'classes', 'startDate', 'endDate', 'selectedClassId', 'filterCategory',
                'class', 'paginatedData', 'stats'
            ));
        }

        // --- MODE 2: MAKRO (Rekapitulasi Semua Kelas) ---
        else {
            $recapData = RefClass::withSum(['incomes' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('date', [$startDate, $endDate]);
                                }], 'amount')
                                ->withSum(['expenses' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('expense_date', [$startDate, $endDate]);
                                }], 'amount')
                                ->orderBy('name')
                                ->paginate(20);

            $grandTotalIncome = SClassIncome::whereBetween('date', [$startDate, $endDate])->sum('amount');
            $grandTotalExpense = SClassExpense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

            return view('pengelola.reports.index', compact(
                'classes', 'startDate', 'endDate', 'selectedClassId', 'filterCategory',
                'recapData', 'grandTotalIncome', 'grandTotalExpense'
            ));
        }
    }

    public function downloadPDF(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedClassId = $request->input('class_id');

        if ($selectedClassId) {
            // --- PDF MIKRO (Harian) ---
            $class = RefClass::find($selectedClassId);

            // Copy Logic dari Index (Tanpa Pagination)
            $prevIncome = SClassIncome::where('class_id', $selectedClassId)->where('date', '<', $startDate)->sum('amount');
            $prevExpense = SClassExpense::where('class_id', $selectedClassId)->where('expense_date', '<', $startDate)->sum('amount');
            $openingBalance = $prevIncome - $prevExpense;

            $incomes = SClassIncome::where('class_id', $selectedClassId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('DATE(date) as day_date, sum(amount) as total_income')
                ->groupBy('day_date')->get()->keyBy('day_date');

            $expenses = SClassExpense::where('class_id', $selectedClassId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->selectRaw('DATE(expense_date) as day_date, sum(amount) as total_expense')
                ->groupBy('day_date')->get()->keyBy('day_date');

            $allDates = $incomes->keys()->merge($expenses->keys())->unique()->sort();

            $runningBalance = $openingBalance;
            $processedData = collect([]);
            
            // Baris Saldo Awal
            $processedData->push((object) [
                'date' => Carbon::parse($startDate)->subDay(),
                'income' => 0,
                'expense' => 0,
                'balance' => $openingBalance,
                'is_opening' => true 
            ]);

            foreach ($allDates as $dateStr) {
                $inc = isset($incomes[$dateStr]) ? $incomes[$dateStr]->total_income : 0;
                $exp = isset($expenses[$dateStr]) ? $expenses[$dateStr]->total_expense : 0;
                $runningBalance += ($inc - $exp);

                $processedData->push((object) [
                    'date' => Carbon::parse($dateStr),
                    'income' => $inc,
                    'expense' => $exp,
                    'balance' => $runningBalance,
                    'is_opening' => false
                ]);
            }

            // Sort DESC untuk PDF juga
            $finalData = $processedData->sortByDesc('date');

            $pdf = Pdf::loadView('pengelola.reports.pdf_micro', [
                'class' => $class,
                'processedData' => $finalData,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->stream('Laporan Harian Kelas ' . $class->full_name . '.pdf');

        } else {
            // --- PDF MAKRO (Tetap Sama) ---
            $recapData = RefClass::withSum(['incomes' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('date', [$startDate, $endDate]);
                                }], 'amount')
                                ->withSum(['expenses' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('expense_date', [$startDate, $endDate]);
                                }], 'amount')
                                ->orderBy('name')->get();

            $grandTotalIncome = $recapData->sum('incomes_sum_amount');
            $grandTotalExpense = $recapData->sum('expenses_sum_amount');

            $pdf = Pdf::loadView('pengelola.reports.pdf_macro', compact('recapData', 'startDate', 'endDate', 'grandTotalIncome', 'grandTotalExpense'));
            return $pdf->stream('Laporan Rekapitulasi Sekolah.pdf');
        }
    }
}