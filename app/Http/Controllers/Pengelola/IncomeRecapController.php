<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassIncome;
use App\Models\RefClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IncomeRecapController extends Controller
{
    public function index(Request $request)
    {
        // 1. Siapkan Data Filter
        $classes = RefClass::orderBy('academic_level')->orderBy('name')->get();
        
        // Default Filter
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        
        // Pagination Setting
        $perPage = $request->input('per_page', 10);
        if ($perPage > 100) $perPage = 100; // Batasi max 100 jika tidak filter kelas

        // 2. Query Builder
        $query = SClassIncome::query()
            ->with(['classRoom', 'creator']) // Eager load relasi
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter Kelas
        if ($classId) {
            $query->where('class_id', $classId);
            $perPage = 1000; // "Unlimited" (batas wajar) jika filter kelas aktif
        }

        // Filter Nominal
        if ($minAmount) {
            $query->where('amount', '>=', str_replace('.', '', $minAmount));
        }
        if ($maxAmount) {
            $query->where('amount', '<=', str_replace('.', '', $maxAmount));
        }

        // Urutkan terbaru
        $query->latest('date')->latest('created_at');

        // Eksekusi Query dengan Pagination
        // Jika classId ada, kita anggap 'unlimited' (get all), tapi tetap pakai paginate agar view konsisten
        // Max 1000 data sesuai request
        $incomes = $query->paginate($perPage);

        // Hitung Total Saldo (dari hasil filter)
        $totalIncome = $query->sum('amount'); // Sum dari query yang sama (tanpa limit pagination)

        return view('pengelola.incomes.recap', compact(
            'classes', 'incomes', 'totalIncome', 
            'startDate', 'endDate', 'classId', 'minAmount', 'maxAmount', 'perPage'
        ));
    }

    public function downloadPDF(Request $request)
    {
        // Ambil filter dari request (Logika sama persis dengan index)
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        
        // Ambil preferensi kolom yang mau ditampilkan
        $showNo = $request->boolean('show_no', true); // Default true
        $showDesc = $request->boolean('show_desc', true); // Default true

        $query = SClassIncome::query()
            ->with(['classRoom', 'creator'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) $query->where('class_id', $classId);
        if ($minAmount) $query->where('amount', '>=', str_replace('.', '', $minAmount));
        if ($maxAmount) $query->where('amount', '<=', str_replace('.', '', $maxAmount));

        $incomes = $query->latest('date')->latest('created_at')->get(); // Ambil semua data
        $totalIncome = $incomes->sum('amount');
        
        $className = $classId ? RefClass::find($classId)->full_name : 'Semua Kelas';

        $pdf = Pdf::loadView('pengelola.incomes.pdf', compact(
            'incomes', 'totalIncome', 'startDate', 'endDate', 'className', 
            'showNo', 'showDesc'
        ));
        
        // Set orientasi Landscape jika kolom banyak, Portrait jika sedikit
        $orientation = ($showDesc) ? 'landscape' : 'portrait';
        $pdf->setPaper('a4', $orientation);

        return $pdf->stream('Rekap Pemasukan - ' . $className . '.pdf');
    }
}