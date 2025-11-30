<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SClassExpense;
use App\Models\RefClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    // Daftar penerima standar
    private $recipients = [
        'ATK (Spidol, Kertas, Tinta)',
        'Uang Kebersihan',
        'Sumbangan OSIS/Kegiatan Sekolah',
        'Fotokopi',
        'Perlengkapan Kelas (Sapu, dll)',
        'Lain-lain',
    ];

    /**
     * Menampilkan daftar pengeluaran dengan filter.
     */
    public function index(Request $request)
    {
        $classes = RefClass::orderBy('academic_level')->orderBy('name')->get();
        
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $classId = $request->input('class_id');
        
        $query = SClassExpense::query()
            ->with(['classRoom', 'creator', 'student'])
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();
        
        // Untuk tombol tambah
        $selectedClass = $classId ? RefClass::find($classId) : null;

        return view('superadmin.expenses.index', compact('classes', 'expenses', 'startDate', 'endDate', 'classId', 'selectedClass'));
    }

    /**
     * Form Tambah Pengeluaran (Wajib pilih kelas dulu di Index).
     */
    public function create(Request $request)
    {
        $classId = $request->query('class_id');
        if (!$classId) {
            return redirect()->route('superadmin.expenses.index')->with('error', 'Pilih kelas terlebih dahulu pada filter untuk menambah pengeluaran.');
        }

        $class = RefClass::findOrFail($classId);
        $recipients = $this->recipients;
        $students = $class->studentsForThisYear()->orderBy('full_name')->get();

        return view('superadmin.expenses.create', compact('class', 'recipients', 'students'));
    }

    /**
     * Simpan Pengeluaran.
     */
    public function store(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:ref_classes,id']);
        $class = RefClass::findOrFail($request->class_id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'recipient' => 'required|string|max:255',
            'student_id' => [
                'nullable',
                'string',
                'exists:ref_students,id',
                Rule::requiredIf($request->recipient == 'Siswa'),
            ],
            'description' => 'required|string|max:255',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'student_id.required' => 'Kolom Siswa wajib diisi jika Penerima adalah "Siswa".'
        ]);

        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $directory = 'proofs/' . Str::slug($class->name);
            $imagePath = $request->file('proof_image')->store($directory, 'public');
        }

        $studentId = ($validated['recipient'] == 'Siswa') ? $validated['student_id'] : null;

        SClassExpense::create([
            'class_id' => $class->id,
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'recipient' => $validated['recipient'],
            'student_id' => $studentId,
            'proof_image' => $imagePath,
            'created_by' => Auth::id(), // Dicatat Super Admin
        ]);

        return redirect()->route('superadmin.expenses.index', ['class_id' => $class->id])->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Form Edit Pengeluaran.
     */
    public function edit($id)
    {
        $expense = SClassExpense::findOrFail($id);
        $class = $expense->classRoom;
        
        $recipients = $this->recipients;
        $students = $class->studentsForThisYear()->orderBy('full_name')->get();

        return view('superadmin.expenses.edit', compact('class', 'expense', 'recipients', 'students'));
    }

    /**
     * Update Pengeluaran.
     */
    public function update(Request $request, $id)
    {
        $expense = SClassExpense::findOrFail($id);
        $class = $expense->classRoom; // Ambil kelas dari data yang diedit
        
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'recipient' => 'required|string|max:255',
            'student_id' => [
                'nullable',
                'string',
                'exists:ref_students,id',
                Rule::requiredIf($request->recipient == 'Siswa'),
            ],
            'description' => 'required|string|max:255',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $expense->proof_image;
        
        if ($request->hasFile('proof_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $directory = 'proofs/' . Str::slug($class->name);
            $imagePath = $request->file('proof_image')->store($directory, 'public');
        }

        $studentId = ($validated['recipient'] == 'Siswa') ? $validated['student_id'] : null;

        $expense->update([
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'recipient' => $validated['recipient'],
            'student_id' => $studentId,
            'proof_image' => $imagePath,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('superadmin.expenses.index', ['class_id' => $class->id])->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Hapus Pengeluaran.
     */
    public function destroy($id)
    {
        $expense = SClassExpense::findOrFail($id);
        try {
            if ($expense->proof_image) {
                Storage::disk('public')->delete($expense->proof_image);
            }
            $expense->delete();
            return back()->with('success', 'Data pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}