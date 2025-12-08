<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\RefClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SuperAdmin\FinancialReportExport;

class ReportController extends Controller
{
   public function index(Request $request)
    {
        // --- 1. LOAD DATA UTAMA UNTUK FILTER ---
        $classes = RefClass::orderBy('academic_level')->orderBy('name')->get();
        $users = User::orderBy('name')->get(); 

        // --- 2. AMBIL PARAMETER FILTER ---
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        $type = $request->input('type'); 
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        $userId = $request->input('user_id');
        $showChart = $request->boolean('show_chart'); // FITUR BARU

        // --- 3. BUILD QUERY (BASE) ---
        // Kita buat query builder dasar agar bisa dipakai ulang untuk Tabel & Grafik
        $incomesQuery = SClassIncome::query()
            ->join('ref_classes', 's_class_incomes.class_id', '=', 'ref_classes.id')
            ->join('core_users', 's_class_incomes.created_by', '=', 'core_users.id')
            ->whereBetween('s_class_incomes.date', [$startDate, $endDate]);

        $expensesQuery = SClassExpense::query()
            ->join('ref_classes', 's_class_expenses.class_id', '=', 'ref_classes.id')
            ->join('core_users', 's_class_expenses.created_by', '=', 'core_users.id')
            ->whereBetween('s_class_expenses.expense_date', [$startDate, $endDate]);

        // --- 4. TERAPKAN FILTER TAMBAHAN (KE DUA QUERY) ---
        if ($classId) {
            $incomesQuery->where('s_class_incomes.class_id', $classId);
            $expensesQuery->where('s_class_expenses.class_id', $classId);
        }
        if ($userId) {
            $incomesQuery->where('s_class_incomes.created_by', $userId);
            $expensesQuery->where('s_class_expenses.created_by', $userId);
        }
        if ($minAmount) {
            $val = str_replace('.', '', $minAmount);
            $incomesQuery->where('s_class_incomes.amount', '>=', $val);
            $expensesQuery->where('s_class_expenses.amount', '>=', $val);
        }
        if ($maxAmount) {
            $val = str_replace('.', '', $maxAmount);
            $incomesQuery->where('s_class_incomes.amount', '<=', $val);
            $expensesQuery->where('s_class_expenses.amount', '<=', $val);
        }

        // --- 5. SIAPKAN DATA GRAFIK (JIKA DIPINTA) ---
        $chartData = null;
        if ($showChart) {
            // Clone query agar tidak merusak query utama untuk tabel
            $chartInc = clone $incomesQuery;
            $chartExp = clone $expensesQuery;

            // Group by Date
            $dailyInc = $chartInc->selectRaw('DATE(s_class_incomes.date) as day, SUM(s_class_incomes.amount) as total')
                ->groupBy('day')->pluck('total', 'day');
            
            $dailyExp = $chartExp->selectRaw('DATE(s_class_expenses.expense_date) as day, SUM(s_class_expenses.amount) as total')
                ->groupBy('day')->pluck('total', 'day');

            // Mapping Periode
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $dates = [];
            $incSeries = [];
            $expSeries = [];

            foreach ($period as $date) {
                $d = $date->format('Y-m-d');
                $dates[] = $date->format('d M');
                
                $incSeries[] = $dailyInc[$d] ?? 0;
                $expSeries[] = $dailyExp[$d] ?? 0;
            }

            // Susun Series Sesuai Filter Tipe
            $series = [];
            if (!$type || $type == 'income') {
                $series[] = ['name' => 'Pemasukan', 'data' => $incSeries];
            }
            if (!$type || $type == 'expense') {
                $series[] = ['name' => 'Pengeluaran', 'data' => $expSeries];
            }

            $chartData = [
                'categories' => $dates,
                'series' => $series
            ];
        }

        // --- 6. DATA UNTUK TABEL (SELECT KOLOM LENGKAP) ---
        // Lanjutkan query utama untuk select detail
        $incomesQuery->select(
            's_class_incomes.date',
            's_class_incomes.amount',
            's_class_incomes.description',
            's_class_incomes.created_at',
            'ref_classes.name as class_name_raw',
            'ref_classes.academic_level',
            'core_users.name as pic_name',
            DB::raw("'income' as type"),
            DB::raw("NULL as recipient")
        );

        $expensesQuery->select(
            's_class_expenses.expense_date as date',
            's_class_expenses.amount',
            's_class_expenses.description',
            's_class_expenses.created_at',
            'ref_classes.name as class_name_raw',
            'ref_classes.academic_level',
            'core_users.name as pic_name',
            DB::raw("'expense' as type"),
            's_class_expenses.recipient'
        );

        // Gabungkan & Urutkan
        if ($type == 'income') {
            $transactions = $incomesQuery->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        } elseif ($type == 'expense') {
            $transactions = $expensesQuery->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        } else {
            $transactions = $incomesQuery->union($expensesQuery)->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        }

        // Transform Nama Kelas
        $transactions->transform(function($item) {
            $item->class_name = $item->academic_level . ' ' . $item->class_name_raw;
            return $item;
        });

        // --- 7. STATISTIK ---
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $stats = [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netCashFlow' => $totalIncome - $totalExpense,
            'transactionCount' => $transactions->count(),
        ];

        // --- 8. EXPORT (SAMA SEPERTI SEBELUMNYA) ---
        if ($request->has('export')) {
            $filters = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'className' => $classId ? RefClass::find($classId)->full_name : 'Semua Kelas',
                'categoryName' => $type ?: 'Semua',
            ];

            if ($request->export == 'pdf') {
                $pdf = Pdf::loadView('superadmin.reports.pdf_ledger', compact('transactions', 'stats', 'filters'));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->stream('Laporan_Keuangan_Lengkap.pdf');
            }
            
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\SuperAdmin\FinancialReportExport($stats, $transactions, $filters), 
                    'Laporan_Keuangan.xlsx'
                );
            }
        }

        return view('superadmin.reports.index', compact(
            'classes', 'users', 'transactions', 'stats', 
            'startDate', 'endDate', 'classId', 'type', 'userId', 'minAmount', 'maxAmount',
            'showChart', 'chartData' // <-- Kirim data chart
        ));
    }
}