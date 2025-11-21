<?php

namespace App\Http\Controllers\WaliKelas;

use Illuminate\Support\Str;
use App\Models\SClassIncome;
use Illuminate\Http\Request;
use App\Models\SClassExpense;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // <-- Pastikan ini ada

class ExpenseController extends Controller
{
    // REVISI: Daftar penerima (dropdown). Anda bisa edit daftar ini.
    // Kita HAPUS "Siswa" dari sini, karena akan jadi opsi spesial di view
    private $recipients = [
        'ATK (Spidol, Kertas, Tinta)',
        'Uang Kebersihan',
        'Sumbangan OSIS/Kegiatan Sekolah',
        'Fotokopi',
        'Perlengkapan Kelas (Sapu, dll)',
        'Lain-lain',
    ];

    private function getWaliKelasClass()
    {
        return Auth::user()->getWaliKelasClass();
    }

    public function index()
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return view('walikelas.expenses.no_class');
        }

        $totalIncome = SClassIncome::where('class_id', $class->id)->sum('amount');
        $totalExpense = SClassExpense::where('class_id', $class->id)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // REVISI: Eager load relasi student()
        $expenses = SClassExpense::where('class_id', $class->id)
                                ->with(['creator', 'student']) // Tambahkan 'student'
                                ->latest('expense_date')
                                ->paginate(20);

        return view('walikelas.expenses.index', compact('class', 'expenses', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function create()
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return redirect()->route('walikelas.expenses.index')->with('error', 'Anda tidak memiliki kelas.');
        }
        
        $recipients = $this->recipients;
        // REVISI: Ambil daftar siswa untuk dropdown
        $students = $class->studentsForThisYear()->orderBy('full_name')->get(); 

        return view('walikelas.expenses.create', compact('class', 'recipients', 'students'));
    }

    public function store(Request $request)
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return redirect()->route('walikelas.expenses.index')->with('error', 'Anda tidak memiliki kelas.');
        }

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'recipient' => 'required|string|max:255',
            // REVISI: student_id wajib jika recipient == "Siswa"
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

        // REVISI: Set student_id ke null jika penerima bukan siswa
        $studentId = ($validated['recipient'] == 'Siswa') ? $validated['student_id'] : null;

        SClassExpense::create([
            'class_id' => $class->id,
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'recipient' => $validated['recipient'],
            'student_id' => $studentId, // Simpan student_id (atau null)
            'proof_image' => $imagePath,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('walikelas.expenses.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(SClassExpense $expense)
    {
        $class = $this->getWaliKelasClass();
        if (!$class || $expense->class_id !== $class->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        if ($expense->recipient == 'Pengelola Keuangan') {
        return redirect()->route('walikelas.expenses.index')->with('error', 'Data penyaluran dana tidak dapat diedit oleh Wali Kelas.');
    }
        
        $recipients = $this->recipients;
        // REVISI: Ambil daftar siswa untuk dropdown
        $students = $class->studentsForThisYear()->orderBy('full_name')->get();

        return view('walikelas.expenses.edit', compact('class', 'expense', 'recipients', 'students'));
    }

    public function update(Request $request, SClassExpense $expense)
    {
        $class = $this->getWaliKelasClass();
        if (!$class || $expense->class_id !== $class->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
        if ($expense->recipient == 'Pengelola Keuangan') {
    return redirect()->route('walikelas.expenses.index')->with('error', 'Data penyaluran dana tidak dapat diubah/dihapus oleh Wali Kelas.');
}
        
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

        $imagePath = $expense->proof_image;
        
        if ($request->hasFile('proof_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $directory = 'proofs/' . Str::slug($class->name);
            $imagePath = $request->file('proof_image')->store($directory, 'public');
        }

        // REVISI: Set student_id ke null jika penerima bukan siswa
        $studentId = ($validated['recipient'] == 'Siswa') ? $validated['student_id'] : null;

        $expense->update([
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'recipient' => $validated['recipient'],
            'student_id' => $studentId, // Simpan student_id (atau null)
            'proof_image' => $imagePath,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('walikelas.expenses.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(SClassExpense $expense)
    {
        // (Logika destroy biarkan sama, tidak perlu diubah)
        $class = $this->getWaliKelasClass();
        if (!$class || $expense->class_id !== $class->id) {
            return redirect()->route('walikelas.expenses.index')->with('error', 'Anda tidak memiliki akses.');
        }
        if ($expense->recipient == 'Pengelola Keuangan') {
    return redirect()->route('walikelas.expenses.index')->with('error', 'Data penyaluran dana tidak dapat diubah/dihapus oleh Wali Kelas.');
}

        try {
            if ($expense->proof_image) {
                Storage::disk('public')->delete($expense->proof_image);
            }
            $expense->delete();
            return redirect()->route('walikelas.expenses.index')->with('success', 'Data pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('walikelas.expenses.index')->with('error', 'Gagal menghapus data.');
        }
    }
}