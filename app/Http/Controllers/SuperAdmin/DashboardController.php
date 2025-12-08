<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefClass;
use App\Models\RefStudent;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $stats = [
            'total_users' => User::count(),
            'total_students' => RefStudent::count(),
            'total_classes' => RefClass::count(),
            'active_homerooms' => User::whereNotNull('class_id')->count(),
        ];

        // 2. Data Grafik: Distribusi Siswa (Pie Chart) - Tetap Sama
        $levelDistribution = RefClass::select('academic_level', DB::raw('count(*) as total'))
                                     ->groupBy('academic_level')
                                     ->pluck('total', 'academic_level');
        
        $pieChartData = [
            'labels' => ['Kelas 10', 'Kelas 11', 'Kelas 12'],
            'series' => [
                $levelDistribution[10] ?? 0,
                $levelDistribution[11] ?? 0,
                $levelDistribution[12] ?? 0,
            ]
        ];

        // 3. Data Grafik: Aktivitas Transaksi 7 Hari Terakhir (UPDATE: Nominal & Frekuensi)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }

        $barChartData = [
            'categories' => $dates->map(fn($date) => Carbon::parse($date)->format('d M'))->toArray(),
            'incomes_amount' => [],
            'incomes_count' => [],
            'expenses_amount' => [],
            'expenses_count' => []
        ];

        foreach ($dates as $date) {
            // Pemasukan
            $dailyIncome = SClassIncome::whereDate('date', $date);
            $barChartData['incomes_amount'][] = $dailyIncome->sum('amount');
            $barChartData['incomes_count'][] = $dailyIncome->count();

            // Pengeluaran
            $dailyExpense = SClassExpense::whereDate('expense_date', $date);
            $barChartData['expenses_amount'][] = $dailyExpense->sum('amount');
            $barChartData['expenses_count'][] = $dailyExpense->count();
        }

        // 4. Tabel Transaksi Terbaru (UPDATE: Ganti dari Kelas ke Transaksi)
        $incomes = SClassIncome::with('classRoom')
            ->select('id', 'date', 'amount', 'description', 'class_id', 'created_at', DB::raw("'income' as type"));
        
        $expenses = SClassExpense::with('classRoom')
            ->select('id', 'expense_date as date', 'amount', 'description', 'class_id', 'created_at', DB::raw("'expense' as type"));

        $recentTransactions = $incomes->union($expenses)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();

        return view('superadmin.dashboard', compact('stats', 'pieChartData', 'barChartData', 'recentTransactions'));
    }
}