<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\RefClass;
use Barryvdh\DomPDF\Facade\Pdf; // Import library PDF

class ReportController extends Controller
{
    /**
     * Menampilkan halaman filter dan hasil laporan (versi Web).
     */
    public function index(Request $request)
    {
        $class = Auth::user()->getWaliKelasClass();
        if (!$class) {
            return view('walikelas.reports.no_class');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $incomes = null;
        $expenses = null;
        $stats = null;

        // Hanya jalankan query jika kedua tanggal sudah diisi
        if ($startDate && $endDate) {
            // Ambil data sesuai rentang tanggal dan kelas
            $incomes = SClassIncome::where('class_id', $class->id)
                                ->whereBetween('date', [$startDate, $endDate])
                                ->with('creator')
                                ->latest('date')
                                ->get();

            $expenses = SClassExpense::where('class_id', $class->id)
                                ->whereBetween('expense_date', [$startDate, $endDate])
                                ->with('creator')
                                ->latest('expense_date')
                                ->get();

            // Hitung statistik
            $totalIncome = $incomes->sum('amount');
            $totalExpense = $expenses->sum('amount');
            $stats = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense,
            ];
        }

        return view('walikelas.reports.index', compact(
            'class', 'incomes', 'expenses', 'stats', 'startDate', 'endDate'
        ));
    }

    /**
     * Membuat dan mengunduh laporan dalam format PDF.
     */
    public function downloadPDF(Request $request)
    {
        $class = Auth::user()->getWaliKelasClass();
        if (!$class) {
            return redirect()->route('walikelas.reports.index')->with('error', 'Anda tidak memiliki kelas.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Wajib ada filter tanggal untuk cetak PDF
        if (!$startDate || !$endDate) {
            return redirect()->route('walikelas.reports.index')->with('error', 'Silakan pilih rentang tanggal terlebih dahulu.');
        }

        // Ambil data (logika yang sama dengan index)
        $incomes = SClassIncome::where('class_id', $class->id)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->with('creator')
                            ->latest('date')
                            ->get();

        $expenses = SClassExpense::where('class_id', $class->id)
                            ->whereBetween('expense_date', [$startDate, $endDate])
                            ->with('creator')
                            ->latest('expense_date')
                            ->get();

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $stats = [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
        ];

        // Siapkan data untuk dikirim ke view PDF
        $data = compact('class', 'incomes', 'expenses', 'stats', 'startDate', 'endDate');

        // Load view PDF khusus (yang akan kita buat di Langkah 4)
        $pdf = Pdf::loadView('walikelas.reports.pdf', $data);

        // Buat nama file
        $filename = 'Laporan Keuangan - ' . $class->name . ' - ' . $startDate . ' sd ' . $endDate . '.pdf';

        // Tampilkan di browser (stream)
        return $pdf->stream($filename);
    }
}