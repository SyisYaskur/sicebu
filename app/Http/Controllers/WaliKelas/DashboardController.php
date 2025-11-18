<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $waliKelas = Auth::user();
        $class = $waliKelas->getWaliKelasClass(); // Mengambil kelas dari helper Model User

        // Jika guru tidak punya kelas, tampilkan dashboard "no class"
        if (!$class) {
            return view('walikelas.dashboard_no_class');
        }

        $class_id = $class->id;
        $today = Carbon::today();

        // 1. REVISI 3: Cek apakah sudah ada pemasukan hari ini
        $hasIncomeToday = SClassIncome::where('class_id', $class_id)
                                    ->whereDate('date', $today)
                                    ->exists();

        // 2. Ambil Statistik untuk Kartu Atas
        $totalIncome = SClassIncome::where('class_id', $class_id)->sum('amount');
        $totalExpense = SClassExpense::where('class_id', $class_id)->sum('amount');

        $stats = [
            'balance' => $totalIncome - $totalExpense,
            'incomeThisMonth' => SClassIncome::where('class_id', $class_id)
                                    ->whereYear('date', $today->year)
                                    ->whereMonth('date', $today->month)
                                    ->sum('amount'),
            'expenseThisMonth' => SClassExpense::where('class_id', $class_id)
                                    ->whereYear('expense_date', $today->year)
                                    ->whereMonth('expense_date', $today->month)
                                    ->sum('amount'),
            'studentCount' => $class->studentsForThisYear()->count(),
        ];

        // 3. Ambil 5 Aktivitas Kas Terbaru (Gabungan Pemasukan & Pengeluaran)
        $incomes = SClassIncome::where('class_id', $class_id)
                            ->select('date', 'description', 'amount', DB::raw("'income' as type"), 'created_at');

        $expenses = SClassExpense::where('class_id', $class_id)
                            ->select('expense_date as date', 'description', 'amount', DB::raw("'expense' as type"), 'created_at');

        $recentActivity = $incomes->union($expenses)
                                ->orderBy('date', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        return view('walikelas.dashboard', compact('class', 'stats', 'hasIncomeToday', 'recentActivity'));
    }
}