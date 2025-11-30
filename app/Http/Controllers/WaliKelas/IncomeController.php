<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\SClassIncome;
use App\Models\SClassExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    private function getWaliKelasClass()
    {
        return Auth::user()->getWaliKelasClass(); // Helper dari Model User
    }

    public function index()
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return view('walikelas.incomes.no_class');
        }

        $incomes = SClassIncome::where('class_id', $class->id)
        ->with('creator') // Ambil data pembuat
        ->latest('date')
        ->paginate(20);
        
        $totalExpense = SClassExpense::where('class_id', $class->id)->sum('amount');
        $totalIncome = SClassIncome::where('class_id', $class->id)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('walikelas.incomes.index', compact('class', 'incomes', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function create()
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return redirect()->route('walikelas.incomes.index')->with('error', 'Anda tidak memiliki kelas.');
        }
        return view('walikelas.incomes.create', compact('class'));
    }

    public function store(Request $request)
    {
        $class = $this->getWaliKelasClass();
        if (!$class) {
            return redirect()->route('walikelas.incomes.index')->with('error', 'Anda tidak memiliki kelas.');
        }

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
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('walikelas.incomes.index')->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function edit(SClassIncome $income)
    {
        $class = $this->getWaliKelasClass();
        if (!$class || $income->class_id !== $class->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('walikelas.incomes.edit', compact('class', 'income'));
    }

    public function update(Request $request, SClassIncome $income)
    {
        $class = $this->getWaliKelasClass();
        if (!$class || $income->class_id !== $class->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

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

        return redirect()->route('walikelas.incomes.index')->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy(SClassIncome $income)
    {
        $class = $this->getWaliKelasClass();
        if (!$class || $income->class_id !== $class->id) {
            return redirect()->route('walikelas.incomes.index')->with('error', 'Anda tidak memiliki akses.');
        }

        $income->delete();
        return redirect()->route('walikelas.incomes.index')->with('success', 'Data pemasukan berhasil dihapus.');
    }
}