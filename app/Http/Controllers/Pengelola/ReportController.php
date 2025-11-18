<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RefClass;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = RefClass::orderBy('name')->get(); // Untuk dropdown filter
        
        $startDate = $request->input('start_date', date('Y-m-01')); // Default awal bulan
        $endDate = $request->input('end_date', date('Y-m-d')); // Default hari ini
        $selectedClassId = $request->input('class_id'); // Bisa null (Semua Kelas) atau ID tertentu

        // --- MODE 1: MIKRO (Detail Satu Kelas) ---
        if ($selectedClassId) {
            $class = RefClass::find($selectedClassId);
            
            // Optimasi: Gunakan paginate() untuk tabel agar tidak berat
            $incomes = SClassIncome::where('class_id', $selectedClassId)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->with('creator') // Eager load user
                                ->latest('date')
                                ->paginate(15, ['*'], 'incomes_page'); // Custom page name

            $expenses = SClassExpense::where('class_id', $selectedClassId)
                                ->whereBetween('expense_date', [$startDate, $endDate])
                                ->with(['creator', 'student']) // Eager load user & student
                                ->latest('expense_date')
                                ->paginate(15, ['*'], 'expenses_page');

            // Hitung total untuk ringkasan (Query terpisah agar tidak terpengaruh pagination)
            $totalIncome = SClassIncome::where('class_id', $selectedClassId)
                                ->whereBetween('date', [$startDate, $endDate])->sum('amount');
            $totalExpense = SClassExpense::where('class_id', $selectedClassId)
                                ->whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
            
            return view('pengelola.reports.index', compact(
                'classes', 'startDate', 'endDate', 'selectedClassId', 
                'class', 'incomes', 'expenses', 'totalIncome', 'totalExpense'
            ));
        }

        // --- MODE 2: MAKRO (Rekapitulasi Semua Kelas) ---
        else {
            // OPTIMASI QUERY: Gunakan withSum agar database yang menghitung, bukan PHP
            // Ini sangat cepat walau ada ribuan data.
            $recapData = RefClass::withSum(['incomes' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('date', [$startDate, $endDate]);
                                }], 'amount')
                                ->withSum(['expenses' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('expense_date', [$startDate, $endDate]);
                                }], 'amount')
                                ->orderBy('name')
                                ->paginate(20); // Pagination agar loading halaman cepat

            // Hitung Grand Total Sekolah
            $grandTotalIncome = SClassIncome::whereBetween('date', [$startDate, $endDate])->sum('amount');
            $grandTotalExpense = SClassExpense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

            return view('pengelola.reports.index', compact(
                'classes', 'startDate', 'endDate', 'selectedClassId', 
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
            // --- CETAK PDF MIKRO (Per Kelas) ---
            $class = RefClass::find($selectedClassId);
            
            $incomes = SClassIncome::where('class_id', $selectedClassId)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->with('creator')->latest('date')->get();
            
            $expenses = SClassExpense::where('class_id', $selectedClassId)
                                ->whereBetween('expense_date', [$startDate, $endDate])
                                ->with(['creator', 'student'])->latest('expense_date')->get();

            // REVISI: Bungkus dalam array $stats agar cocok dengan view
            $stats = [
                'totalIncome' => $incomes->sum('amount'),
                'totalExpense' => $expenses->sum('amount'),
                'balance' => $incomes->sum('amount') - $expenses->sum('amount'),
            ];

            // REVISI: Kirim 'stats' ke view, bukan variabel terpisah
            $pdf = Pdf::loadView('pengelola.reports.pdf_micro', compact('class', 'incomes', 'expenses', 'startDate', 'endDate', 'stats'));
            
            return $pdf->stream('Laporan Kelas ' . $class->full_name . '.pdf'); // Pakai full_name juga di sini biar rapi
        } else {
            // --- CETAK PDF MAKRO (Semua Kelas) ---
            // Ambil SEMUA data kelas (get) tanpa pagination
            $recapData = RefClass::withSum(['incomes' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('date', [$startDate, $endDate]);
                                }], 'amount')
                                ->withSum(['expenses' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('expense_date', [$startDate, $endDate]);
                                }], 'amount')
                                ->orderBy('name')
                                ->get();

            $grandTotalIncome = $recapData->sum('incomes_sum_amount');
            $grandTotalExpense = $recapData->sum('expenses_sum_amount');

            $pdf = Pdf::loadView('pengelola.reports.pdf_macro', compact('recapData', 'startDate', 'endDate', 'grandTotalIncome', 'grandTotalExpense'));
            return $pdf->stream('Laporan Rekapitulasi Sekolah.pdf');
        }
    }
}