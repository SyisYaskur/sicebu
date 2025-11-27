<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\SDisbursement;
use App\Models\RefClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // --- 1. KARTU STATISTIK UTAMA ---
        $totalIncome = SClassIncome::sum('amount');
        $totalExpense = SClassExpense::sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $monthlyIncome = SClassIncome::whereYear('date', $today->year)
                            ->whereMonth('date', $today->month)
                            ->sum('amount');
        
        $monthlyExpense = SClassExpense::whereYear('expense_date', $today->year)
                            ->whereMonth('expense_date', $today->month)
                            ->sum('amount');

        $monthlyDisbursement = SDisbursement::whereYear('disbursement_date', $today->year)
                            ->whereMonth('disbursement_date', $today->month)
                            ->sum('total_amount');

        $stats = [
            'totalBalance' => $totalBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'monthlyDisbursement' => $monthlyDisbursement
        ];

        // --- 2. CHART TREN 12 BULAN TERAKHIR ---
        $chartData = [
            'labels' => [],
            'incomes' => [],
            'expenses' => []
        ];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $monthName = $date->format('M Y');
            $year = $date->year;
            $month = $date->month;

            $inc = SClassIncome::whereYear('date', $year)->whereMonth('date', $month)->sum('amount');
            $exp = SClassExpense::whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');

            $chartData['labels'][] = $monthName;
            $chartData['incomes'][] = $inc;
            $chartData['expenses'][] = $exp;
        }

        // --- 3. LEADERBOARD KELAS (Saldo Tertinggi & Terendah) ---
        // Kita ambil semua kelas, hitung saldonya, lalu urutkan di Collection (PHP)
        // Cara ini aman jika jumlah kelas < 1000.
        $classes = RefClass::withSum('incomes', 'amount')
                           ->withSum('expenses', 'amount')
                           ->get()
                           ->map(function ($class) {
                               $class->balance = $class->incomes_sum_amount - $class->expenses_sum_amount;
                               return $class;
                           });

        $topClasses = $classes->sortByDesc('balance')->take(5);
        $lowClasses = $classes->sortBy('balance')->take(5);

        // --- 4. AKTIVITAS TERAKHIR (Penyaluran) ---
        $recentDisbursements = SDisbursement::with('creator')
                                    ->latest('disbursement_date')
                                    ->take(5)
                                    ->get();

        return view('pengelola.dashboard', compact(
            'stats', 'chartData', 'topClasses', 'lowClasses', 'recentDisbursements'
        ));
    }
}