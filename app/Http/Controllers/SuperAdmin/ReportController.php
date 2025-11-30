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
        $users = User::orderBy('name')->get(); // Untuk filter pencatat

        // --- 2. AMBIL PARAMETER FILTER ---
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        $type = $request->input('type'); // income, expense, all
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        $userId = $request->input('user_id');
        $viewMode = $request->input('view_mode', 'ledger'); // ledger / recap

        // --- 3. BUILD QUERY (GABUNGAN INCOME & EXPENSE) ---
        // Kita gunakan Union agar bisa di-filter bersamaan
        
        $incomes = SClassIncome::query()
            ->join('ref_classes', 's_class_incomes.class_id', '=', 'ref_classes.id')
            ->join('core_users', 's_class_incomes.created_by', '=', 'core_users.id')
            ->select(
                's_class_incomes.date',
                's_class_incomes.amount',
                's_class_incomes.description',
                's_class_incomes.created_at',
                's_class_incomes.class_id',
                's_class_incomes.created_by',
                'ref_classes.name as class_name_raw', // Perlu digabung manual nanti jika ingin full_name via query
                'ref_classes.academic_level',
                'core_users.name as pic_name',
                DB::raw("'income' as type"),
                DB::raw("NULL as recipient")
            )
            ->whereBetween('s_class_incomes.date', [$startDate, $endDate]);

        $expenses = SClassExpense::query()
            ->join('ref_classes', 's_class_expenses.class_id', '=', 'ref_classes.id')
            ->join('core_users', 's_class_expenses.created_by', '=', 'core_users.id')
            ->select(
                's_class_expenses.expense_date as date',
                's_class_expenses.amount',
                's_class_expenses.description',
                's_class_expenses.created_at',
                's_class_expenses.class_id',
                's_class_expenses.created_by',
                'ref_classes.name as class_name_raw',
                'ref_classes.academic_level',
                'core_users.name as pic_name',
                DB::raw("'expense' as type"),
                's_class_expenses.recipient'
            )
            ->whereBetween('s_class_expenses.expense_date', [$startDate, $endDate]);

        // --- 4. TERAPKAN FILTER TAMBAHAN ---
        if ($classId) {
            $incomes->where('s_class_incomes.class_id', $classId);
            $expenses->where('s_class_expenses.class_id', $classId);
        }
        if ($userId) {
            $incomes->where('s_class_incomes.created_by', $userId);
            $expenses->where('s_class_expenses.created_by', $userId);
        }
        if ($minAmount) {
            $val = str_replace('.', '', $minAmount);
            $incomes->where('s_class_incomes.amount', '>=', $val);
            $expenses->where('s_class_expenses.amount', '>=', $val);
        }
        if ($maxAmount) {
            $val = str_replace('.', '', $maxAmount);
            $incomes->where('s_class_incomes.amount', '<=', $val);
            $expenses->where('s_class_expenses.amount', '<=', $val);
        }

        // --- 5. EKSEKUSI QUERY SESUAI TIPE ---
        if ($type == 'income') {
            $transactions = $incomes->orderBy('date', 'desc')
                                    ->orderBy('created_at', 'desc') // Tambahan: jika tanggal sama, urutkan jam
                                    ->get();
        } elseif ($type == 'expense') {
            $transactions = $expenses->orderBy('date', 'desc')
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        } else {
            $transactions = $incomes->union($expenses)
                                    ->orderBy('date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        }

        // Fix Nama Kelas (Gabung manual karena Union Query RAW tidak support Accessor otomatis saat select)
        $transactions->transform(function($item) {
            $item->class_name = $item->academic_level . ' ' . $item->class_name_raw;
            return $item;
        });

        // --- 6. HITUNG STATISTIK ---
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $stats = [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netCashFlow' => $totalIncome - $totalExpense,
            'transactionCount' => $transactions->count(),
        ];

        // --- 7. EXPORT LOGIC ---
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
                return Excel::download(new FinancialReportExport($stats, $transactions, $filters), 'Laporan_Keuangan.xlsx');
            }
        }

        // --- 8. RETURN VIEW ---
        return view('superadmin.reports.index', compact(
            'classes', 'users', 'transactions', 'stats', 
            'startDate', 'endDate', 'classId', 'type', 'userId', 'minAmount', 'maxAmount'
        ));
    }
}