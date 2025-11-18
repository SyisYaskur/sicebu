<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\RefClass;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // --- 1. STATISTIK GLOBAL (Untuk Hero Section) ---
        $globalTotalIncome = SClassIncome::sum('amount');
        $globalTotalExpense = SClassExpense::sum('amount');

        $globalStats = [
            'total_balance' => $globalTotalIncome - $globalTotalExpense,
            'income_this_month' => SClassIncome::whereYear('date', $today->year)
                                        ->whereMonth('date', $today->month)
                                        ->sum('amount'),
            'expense_this_month' => SClassExpense::whereYear('expense_date', $today->year)
                                        ->whereMonth('expense_date', $today->month)
                                        ->sum('amount'),
        ];

        // --- 2. DATA UNTUK FILTER LAPORAN ---
        $classes = RefClass::orderBy('name')->get();

        // Ambil filter dari request
        $selectedClassId = $request->input('class_id');
        $selectedMonth = $request->input('month', $today->format('Y-m')); // Default bulan ini

        $reportData = null;

        // Jika user memilih kelas, siapkan datanya
        if ($selectedClassId) {
            $selectedClass = RefClass::find($selectedClassId);

            if ($selectedClass) {
                $year = Carbon::parse($selectedMonth)->year;
                $month = Carbon::parse($selectedMonth)->month;

                // Saldo Kelas Tersebut
                $classIncome = SClassIncome::where('class_id', $selectedClassId)->sum('amount');
                $classExpense = SClassExpense::where('class_id', $selectedClassId)->sum('amount');
                $classBalance = $classIncome - $classExpense;

                // Riwayat Transaksi (Gabungan Income & Expense) pada bulan yang dipilih
                // Note: Kita menyembunyikan detail siswa untuk privasi publik

                $incomes = SClassIncome::where('class_id', $selectedClassId)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->select('date', 'amount', DB::raw("'Pemasukan Harian' as description"), DB::raw("'income' as type"));

                $expenses = SClassExpense::where('class_id', $selectedClassId)
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $month)
                    ->select('expense_date as date', 'amount', 'description', DB::raw("'expense' as type"));

                $transactions = $incomes->union($expenses)
                    ->orderBy('date', 'desc')
                    ->get();

                $reportData = [
                    'class_name' => $selectedClass->name,
                    'balance' => $classBalance,
                    'transactions' => $transactions
                ];
            }
        }

        return view('welcome', compact('globalStats', 'classes', 'reportData', 'selectedClassId', 'selectedMonth'));
    }
}