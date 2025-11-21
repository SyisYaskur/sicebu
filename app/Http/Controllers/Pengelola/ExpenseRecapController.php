<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassExpense; // Model Pengeluaran
use App\Models\RefClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExpenseRecapController extends Controller
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
        if ($perPage > 100) $perPage = 100;

        // 2. Query Builder
        $query = SClassExpense::query()
            ->with(['classRoom', 'creator', 'student']) // Eager load (tambah student jika penerima siswa)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        // Filter Kelas
        if ($classId) {
            $query->where('class_id', $classId);
            $perPage = 1000; // Unlimited jika filter kelas
        }

        // Filter Nominal
        if ($minAmount) {
            $query->where('amount', '>=', str_replace('.', '', $minAmount));
        }
        if ($maxAmount) {
            $query->where('amount', '<=', str_replace('.', '', $maxAmount));
        }

        // Urutkan terbaru
        $query->latest('expense_date')->latest('created_at');

        // Eksekusi Query
        $expenses = $query->paginate($perPage);
        $totalExpense = $query->sum('amount');

        return view('pengelola.expenses.recap', compact(
            'classes', 'expenses', 'totalExpense', 
            'startDate', 'endDate', 'classId', 'minAmount', 'maxAmount', 'perPage'
        ));
    }

    public function downloadPDF(Request $request)
    {
        // Ambil filter (sama dengan index)
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        
        $showNo = $request->boolean('show_no', true);
        $showDesc = $request->boolean('show_desc', true);

        $query = SClassExpense::query()
            ->with(['classRoom', 'creator', 'student'])
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($classId) $query->where('class_id', $classId);
        if ($minAmount) $query->where('amount', '>=', str_replace('.', '', $minAmount));
        if ($maxAmount) $query->where('amount', '<=', str_replace('.', '', $maxAmount));

        $expenses = $query->latest('expense_date')->latest('created_at')->get();
        $totalExpense = $expenses->sum('amount');
        
        $className = $classId ? RefClass::find($classId)->full_name : 'Semua Kelas';

        $pdf = Pdf::loadView('pengelola.expenses.pdf', compact(
            'expenses', 'totalExpense', 'startDate', 'endDate', 'className', 
            'showNo', 'showDesc'
        ));
        
        $orientation = ($showDesc) ? 'landscape' : 'portrait';
        $pdf->setPaper('a4', $orientation);

        return $pdf->stream('Rekap Pengeluaran - ' . $className . '.pdf');
    }
}