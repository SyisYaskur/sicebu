<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\RefClass;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use App\Models\SDisbursement;
use App\Models\SFundAllocation;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DisbursementController extends Controller
{
    public function index(Request $request)
    {
        // Ambil keyword pencarian
        $search = $request->input('search');

        // Query Builder
        $query = SDisbursement::with('creator');

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Cari berdasarkan Tujuan
                $q->where('purpose', 'like', "%{$search}%")
                  // Cari berdasarkan Tanggal (format YYYY-MM-DD)
                  ->orWhere('disbursement_date', 'like', "%{$search}%")
                  // Cari berdasarkan Catatan
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Urutkan dan Paginate
        $disbursements = $query->latest('disbursement_date')
                               ->paginate(10)
                               ->withQueryString(); // Agar parameter search tidak hilang saat pindah halaman

        return view('superadmin.disbursements.index', compact('disbursements', 'search'));
    }

    private function getClassesWithBalance()
    {
        return RefClass::orderBy('academic_level')->orderBy('name')->get()->map(function ($class) {
            $income = SClassIncome::where('class_id', $class->id)->sum('amount');
            $expense = SClassExpense::where('class_id', $class->id)->sum('amount');
            $class->current_balance = $income - $expense;
            $class->search_label = $class->full_name . ' (Saldo: Rp ' . number_format($class->current_balance, 0, ',', '.') . ')';
            return $class;
        });
    }

    public function create()
    {
        $classes = $this->getClassesWithBalance();
        return view('superadmin.disbursements.create', compact('classes'));
    }

    public function store(Request $request)
    {
        // Cleaning format angka (hapus titik)
        $request->merge([
            'total_amount' => str_replace('.', '', $request->total_amount),
            'allocations' => array_map(function ($item) {
                $item['amount'] = str_replace('.', '', $item['amount']);
                return $item;
            }, $request->allocations ?? [])
        ]);

        $request->validate([
            'purpose' => 'required|string|max:255',
            'disbursement_date' => 'required|date',
            'total_amount' => 'required|numeric|min:1',
            'allocations' => 'required|array|min:1',
            'allocations.*.class_id' => 'required|exists:ref_classes,id',
            'allocations.*.amount' => 'required|numeric|min:1',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Validasi Total & Saldo
        $totalAllocated = 0;
        foreach ($request->allocations as $allocation) {
            $classId = $allocation['class_id'];
            $amount = $allocation['amount'];
            
            $income = SClassIncome::where('class_id', $classId)->sum('amount');
            $expense = SClassExpense::where('class_id', $classId)->sum('amount');
            $balance = $income - $expense;

            if ($amount > $balance) {
                $class = RefClass::find($classId);
                throw ValidationException::withMessages(['allocations' => "Saldo kelas {$class->full_name} tidak mencukupi."]);
            }
            $totalAllocated += $amount;
        }

        if ($totalAllocated != $request->total_amount) {
             throw ValidationException::withMessages(['total_amount' => "Total rincian (Rp ".number_format($totalAllocated).") tidak sama dengan Total Penyaluran."]);
        }

        // Upload Foto
        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $imagePath = $request->file('proof_image')->store('disbursement_proofs', 'public');
        }

        DB::transaction(function () use ($request, $imagePath) {
            $disbursement = SDisbursement::create([
                'purpose' => $request->purpose,
                'total_amount' => $request->total_amount,
                'disbursement_date' => $request->disbursement_date,
                'notes' => $request->notes,
                'proof_image' => $imagePath,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->allocations as $allocation) {
                $classId = $allocation['class_id'];
                $amount = $allocation['amount'];

                // 1. HITUNG SALDO SAAT INI (Snapshot)
                $income = SClassIncome::where('class_id', $classId)->sum('amount');
                $expense = SClassExpense::where('class_id', $classId)->sum('amount');
                $currentBalance = $income - $expense; // Ini adalah Saldo Sebelum
                $balanceAfter = $currentBalance - $amount; // Ini adalah Saldo Sesudah

                // 2. Buat Pengeluaran di Buku Kas Kelas
                $classExpense = SClassExpense::create([
                    'class_id' => $classId,
                    'expense_date' => $request->disbursement_date,
                    'amount' => $amount,
                    'description' => 'Penyaluran Dana: ' . $request->purpose,
                    'recipient' => 'Pengelola Keuangan',
                    'created_by' => Auth::id(),
                ]);

                // 3. Simpan Alokasi BESERTA Snapshot Saldo
                SFundAllocation::create([
                    'disbursement_id' => $disbursement->id,
                    'class_id' => $classId,
                    'amount_transferred' => $amount,
                    'class_expense_id' => $classExpense->id,
                    'balance_before' => $currentBalance, // Simpan
                    'balance_after' => $balanceAfter,   // Simpan
                ]);
            }
        });

        return redirect()->route('superadmin.disbursements.index')->with('success', 'Penyaluran dana berhasil dicatat.');
    }

    public function edit(SDisbursement $disbursement)
    {
        $disbursement->load('allocations');
        $classes = $this->getClassesWithBalance();
        return view('superadmin.disbursements.edit', compact('disbursement', 'classes'));
    }

    public function update(Request $request, SDisbursement $disbursement)
    {
        // Cleaning format angka
        $request->merge([
            'total_amount' => str_replace('.', '', $request->total_amount),
            'allocations' => array_map(function ($item) {
                $item['amount'] = str_replace('.', '', $item['amount']);
                return $item;
            }, $request->allocations ?? [])
        ]);
        
        $request->validate([
            'purpose' => 'required|string|max:255',
            'disbursement_date' => 'required|date',
            'total_amount' => 'required|numeric|min:1',
            'allocations' => 'required|array|min:1',
            'allocations.*.class_id' => 'required|exists:ref_classes,id',
            'allocations.*.amount' => 'required|numeric|min:1',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Validasi Total
        $totalAllocated = 0;
        foreach ($request->allocations as $allocation) {
            $totalAllocated += $allocation['amount'];
        }
        if ($totalAllocated != $request->total_amount) {
             throw ValidationException::withMessages(['total_amount' => "Total rincian tidak sama dengan Total Penyaluran."]);
        }

        // Upload Foto Baru (Jika ada)
        $imagePath = $disbursement->proof_image;
        if ($request->hasFile('proof_image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('proof_image')->store('disbursement_proofs', 'public');
        }

        DB::transaction(function () use ($request, $disbursement, $imagePath) {
            // PERBAIKAN UTAMA DI SINI: Hapus Data Lama dengan Benar
            
            // 1. Ambil semua alokasi lama dari database
            $oldAllocations = $disbursement->allocations()->get();

            foreach ($oldAllocations as $oldAlloc) {
                // Hapus data pengeluaran di buku kas kelas (s_class_expenses)
                // Kita cari berdasarkan ID yang tersimpan di s_fund_allocations
                if ($oldAlloc->class_expense_id) {
                    SClassExpense::where('id', $oldAlloc->class_expense_id)->delete();
                }
            }
            
            // 2. Hapus semua record alokasi lama di s_fund_allocations
            $disbursement->allocations()->delete();

            // 3. Update Header Penyaluran
            $disbursement->update([
                'purpose' => $request->purpose,
                'total_amount' => $request->total_amount,
                'disbursement_date' => $request->disbursement_date,
                'notes' => $request->notes,
                'proof_image' => $imagePath,
                'updated_by' => Auth::id(),
            ]);

            // 4. Buat Ulang Alokasi Baru
            foreach ($request->allocations as $allocationData) {
                $classId = $allocationData['class_id'];
                $amount = $allocationData['amount'];

                // 1. HITUNG SALDO ULANG (Karena data lama sudah dihapus di langkah A, saldo ini akurat)
                $income = SClassIncome::where('class_id', $classId)->sum('amount');
                $expense = SClassExpense::where('class_id', $classId)->sum('amount');
                $currentBalance = $income - $expense;
                $balanceAfter = $currentBalance - $amount;

                // Validasi Saldo (Double Check)
                if ($amount > $currentBalance) {
                     $class = RefClass::find($classId);
                     throw ValidationException::withMessages(['allocations' => "Gagal Update: Saldo kelas {$class->full_name} tidak mencukupi."]);
                }

                // 2. Buat Pengeluaran Baru
                $classExpense = SClassExpense::create([
                    'class_id' => $classId,
                    'expense_date' => $request->disbursement_date,
                    'amount' => $amount,
                    'description' => 'Penyaluran Dana: ' . $request->purpose,
                    'recipient' => 'Pengelola Keuangan',
                    'created_by' => $disbursement->created_by,
                    'updated_by' => Auth::id(),
                ]);

                // 3. Simpan Alokasi dengan Snapshot
                SFundAllocation::create([
                    'disbursement_id' => $disbursement->id,
                    'class_id' => $classId,
                    'amount_transferred' => $amount,
                    'class_expense_id' => $classExpense->id,
                    'balance_before' => $currentBalance, // Simpan
                    'balance_after' => $balanceAfter,   // Simpan
                ]);
            }
        });

        return redirect()->route('superadmin.disbursements.index')->with('success', 'Penyaluran dana berhasil diperbarui.');
    }

    public function destroy(SDisbursement $disbursement)
    {
        DB::transaction(function () use ($disbursement) {
            // PERBAIKAN UTAMA DI SINI:
            // Sebelum menghapus induk, kita loop anak-anaknya dulu
            
            // 1. Ambil semua alokasi
            $allocations = $disbursement->allocations()->get();
            
            foreach ($allocations as $allocation) {
                // 2. Hapus data pengeluaran di buku kas kelas terkait
                // Ini akan mengembalikan saldo kelas karena pengeluarannya dihapus
                if ($allocation->class_expense_id) {
                    SClassExpense::where('id', $allocation->class_expense_id)->delete();
                }
            }

            // 3. Hapus foto jika ada
            if ($disbursement->proof_image) {
                Storage::disk('public')->delete($disbursement->proof_image);
            }

            // 4. Hapus induk (Allocations akan terhapus otomatis karena cascade delete di database,
            // tapi langkah 2 tetap wajib dilakukan manual karena Expense tidak punya cascade ke Allocation)
            $disbursement->delete();
        });

        return redirect()->route('superadmin.disbursements.index')->with('success', 'Penyaluran dibatalkan. Dana telah dikembalikan ke saldo kelas.');
    }

    public function show(SDisbursement $disbursement)
    {
        $disbursement->load('allocations.classRoom');
        return view('superadmin.disbursements.show', compact('disbursement'));
    }

    public function downloadPDF(SDisbursement $disbursement)
    {
        // Load relasi yang dibutuhkan
        $disbursement->load(['allocations.classRoom', 'creator']);
        
        // Load View PDF
        $pdf = Pdf::loadView('superadmin.disbursements.pdf', compact('disbursement'));
        
        // Atur ukuran kertas jika perlu (default A4 Portrait)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan Penyaluran - ' . $disbursement->purpose . '.pdf');
    }
}