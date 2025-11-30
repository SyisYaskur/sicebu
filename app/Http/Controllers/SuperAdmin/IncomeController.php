<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassIncome;
use App\Models\RefClass;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Menampilkan daftar pemasukan dengan filter.
     */
    public function index(Request $request)
    {
        $classes = RefClass::orderBy('academic_level')->orderBy('name')->get();
        
        // Filter Default
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        
        // Query Data
        $query = SClassIncome::query()
            ->with(['classRoom', 'creator']) // Eager load
            ->whereBetween('date', [$startDate, $endDate]);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $incomes = $query->latest('date')->paginate(20)->withQueryString();
        
        // Cek kelas terpilih (untuk memunculkan tombol Tambah)
        $selectedClass = $classId ? RefClass::find($classId) : null;

        return view('superadmin.incomes.index', compact('classes', 'incomes', 'startDate', 'endDate', 'classId', 'selectedClass'));
    }

    /**
     * Form Tambah Pemasukan (Butuh ID Kelas dari URL).
     */
    public function create(Request $request)
    {
        $classId = $request->query('class_id');
        
        // Validasi: Super Admin harus pilih kelas dulu di halaman Index
        if (!$classId) {
            return redirect()->route('superadmin.incomes.index')->with('error', 'Silakan pilih kelas terlebih dahulu pada filter untuk menambah pemasukan.');
        }

        $class = RefClass::findOrFail($classId);

        return view('superadmin.incomes.create', compact('class'));
    }

    /**
     * Simpan Pemasukan Baru.
     */
    public function store(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:ref_classes,id']);
        $class = RefClass::findOrFail($request->class_id);

        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        SClassIncome::create([
            'class_id' => $class->id,
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'created_by' => Auth::id(), // Dicatat oleh Super Admin
        ]);

        return redirect()->route('superadmin.incomes.index', ['class_id' => $class->id])->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    /**
     * Form Edit Pemasukan.
     */
    public function edit($id)
    {
        $income = SClassIncome::findOrFail($id);
        $class = $income->classRoom;
        
        return view('superadmin.incomes.edit', compact('class', 'income'));
    }

    /**
     * Update Pemasukan.
     */
    public function update(Request $request, $id)
    {
        $income = SClassIncome::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $income->update([
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('superadmin.incomes.index', ['class_id' => $income->class_id])->with('success', 'Data pemasukan berhasil diperbarui.');
    }

    /**
     * Hapus Pemasukan.
     */
    public function destroy($id)
    {
        $income = SClassIncome::findOrFail($id);
        $income->delete();

        return back()->with('success', 'Data pemasukan berhasil dihapus.');
    }
}