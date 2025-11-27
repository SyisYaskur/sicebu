<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RefClass;
use App\Models\User;
use App\Models\RefStudent;
use App\Models\RefStudentAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function index()
    {
        // Menggunakan paginate untuk halaman index
        $classes = RefClass::with(['expertiseProgram', 'expertiseConcentration'])
                           ->latest()
                           ->paginate(10);
        return view('superadmin.classes.index', compact('classes'));
    }

    public function create()
    {
        $programs = \App\Models\CoreExpertiseProgram::orderBy('name')->get();
        $concentrations = \App\Models\CoreExpertiseConcentration::orderBy('name')->get();
        
        // Ambil guru yang belum punya kelas (opsional, atau semua guru)
        $teachers = User::whereHas('roles', function($q) {
            $q->where('code', 'guru');
        })->orderBy('name')->get();

        return view('superadmin.classes.create', compact('programs', 'concentrations', 'teachers'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'academic_level' => 'required|integer',
            'academic_year' => ['required', 'string'],
            'expertise_concentration_id' => 'required|exists:core_expertise_concentrations,id',
            'user_id' => [
                'nullable',
                'exists:core_users,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $teacher = User::find($value);
                        if ($teacher->class_id) {
                             $otherClass = RefClass::find($teacher->class_id);
                             $className = $otherClass ? $otherClass->full_name : 'kelas lain';
                             $fail('Guru ini sudah menjadi wali kelas di ' . $className);
                        }
                    }
                },
            ],
            'expertise_program_id' => 'nullable', // Opsional, bisa diisi manual
        ]);

        // Isi Nama Guru Otomatis
        if ($request->user_id) {
            $teacher = User::find($request->user_id);
            $validatedData['teacher_name'] = $teacher->name;
        }

        $validatedData['created_by'] = Auth::id();

        // Simpan Kelas
        $class = RefClass::create($validatedData);

        // Update User (Guru) agar punya class_id
        if ($request->user_id) {
            $teacher = User::find($request->user_id);
            $teacher->class_id = $class->id;
            $teacher->save();
        }

        return redirect()->route('superadmin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(RefClass $class)
    {
        $programs = \App\Models\CoreExpertiseProgram::orderBy('name')->get();
        $concentrations = \App\Models\CoreExpertiseConcentration::orderBy('name')->get();
        
        // Ambil semua guru
        $teachers = User::whereHas('roles', function($q) {
            $q->where('code', 'guru');
        })->orderBy('name')->get();

        // REVISI: Cari Wali Kelas saat ini dari tabel User
        $currentTeacher = User::where('class_id', $class->id)->first();
        $currentTeacherId = $currentTeacher ? $currentTeacher->id : null;

        // Data siswa
        $allStudents = \App\Models\RefStudent::orderBy('full_name')->get();
        $assignedStudentIds = $class->studentsForThisYear()->pluck('ref_students.id')->toArray();

        return view('superadmin.classes.edit', compact(
            'class', 'programs', 'concentrations', 'teachers', 
            'allStudents', 'assignedStudentIds', 'currentTeacherId' // <-- Kirim ID Guru
        ));
    }

    public function update(Request $request, RefClass $class)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'academic_level' => 'required|integer',
            'academic_year' => ['required', 'string'],
            'expertise_concentration_id' => 'required|exists:core_expertise_concentrations,id',
            'user_id' => [
                'nullable',
                'exists:core_users,id',
                // REVISI VALIDASI: Cek di tabel users, apakah guru ini sudah punya kelas LAIN?
                function ($attribute, $value, $fail) use ($class) {
                    if ($value) {
                        // Cari user (guru) tersebut
                        $teacher = User::find($value);
                        // Jika guru sudah punya class_id DAN class_id nya bukan kelas yang sedang diedit ini
                        if ($teacher->class_id && $teacher->class_id !== $class->id) {
                            // Ambil info kelasnya untuk pesan error
                            $otherClass = RefClass::find($teacher->class_id);
                            $className = $otherClass ? $otherClass->full_name : 'kelas lain';
                            $fail('Guru ini sudah menjadi wali kelas di ' . $className);
                        }
                    }
                },
            ],
            'nip_number' => 'nullable|string',
            'nuptk_number' => 'nullable|string',
            'new_student_id' => 'nullable|exists:ref_students,id',
            'remove_student_id' => 'nullable|exists:ref_students,id',
        ]);

        // 2. Update Data Wali Kelas (Logika Tukar Guru)
        // Cari guru lama (siapapun yang class_id nya ke kelas ini)
        $oldTeacher = User::where('class_id', $class->id)->first();

        if ($request->user_id) {
            // Jika ada guru baru dipilih
            $newTeacher = User::find($request->user_id);
            
            // Update nama di tabel kelas
            $validatedData['teacher_name'] = $newTeacher->name;

            // Jika gurunya berbeda dengan yang lama
            if (!$oldTeacher || $oldTeacher->id !== $newTeacher->id) {
                // Lepas guru lama (jika ada)
                if ($oldTeacher) {
                    $oldTeacher->class_id = null;
                    $oldTeacher->save();
                }
                // Pasang guru baru
                $newTeacher->class_id = $class->id;
                $newTeacher->save();
            }
        } else {
            // Jika guru dikosongkan
            $validatedData['teacher_name'] = null;
            // Lepas guru lama
            if ($oldTeacher) {
                $oldTeacher->class_id = null;
                $oldTeacher->save();
            }
        }

        // 3. Update Data Kelas Sisanya
        $validatedData['updated_by'] = Auth::id();
        // Hapus user_id dari array validated karena kolom itu TIDAK ADA di tabel ref_classes
        unset($validatedData['user_id']); 
        unset($validatedData['new_student_id']);
        unset($validatedData['remove_student_id']);
        
        // FIX: Pastikan expertise_program_id ada (opsional, ambil dari request atau null)
        $validatedData['expertise_program_id'] = $request->expertise_program_id ?? null;

        $class->update($validatedData);

        // 4. Manajemen Siswa (Sama seperti sebelumnya)
        if ($request->new_student_id) {
            $exists = \App\Models\RefStudentAcademicYear::where('student_id', $request->new_student_id)
                        ->where('academic_year', $class->academic_year)->exists();
            if (!$exists) {
                \App\Models\RefStudentAcademicYear::create([
                    'id' => (string) Str::uuid(),
                    'student_id' => $request->new_student_id,
                    'class_id' => $class->id,
                    'academic_year' => $class->academic_year,
                    'created_by' => Auth::id()
                ]);
            }
        }
        

        if ($request->remove_student_id) {
            \App\Models\RefStudentAcademicYear::where('student_id', $request->remove_student_id)
                ->where('class_id', $class->id)->where('academic_year', $class->academic_year)->delete();
        }

        return redirect()->route('superadmin.classes.show', $class->id)
                         ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * FUNGSI BARU: Khusus Tambah Siswa
     */
    public function addStudent(Request $request, RefClass $class)
    {
        $request->validate([
            'student_id' => 'required|exists:ref_students,id',
        ]);

        // Cek apakah siswa sudah ada di kelas ini pada tahun ajaran ini
        $exists = \App\Models\RefStudentAcademicYear::where('student_id', $request->student_id)
                    ->where('academic_year', $class->academic_year)
                    ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Siswa tersebut sudah terdaftar di kelas ini atau kelas lain pada tahun ajaran ' . $class->academic_year);
        }

        // Simpan dengan ID UUID Manual (Mengatasi Error 1364)
        \App\Models\RefStudentAcademicYear::create([
            'id' => (string) Str::uuid(), 
            'student_id' => $request->student_id,
            'class_id' => $class->id,
            'academic_year' => $class->academic_year,
            'created_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudent(RefClass $class, $studentId)
    {
        \App\Models\RefStudentAcademicYear::where('student_id', $studentId)
            ->where('class_id', $class->id)
            ->where('academic_year', $class->academic_year)
            ->delete();

        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function destroy(RefClass $class)
    {
        // Gunakan Transaksi Database agar aman (semua terhapus atau tidak sama sekali)
        \Illuminate\Support\Facades\DB::transaction(function () use ($class) {
            
            // 1. Lepas Guru (Analogi: Ambil Kunci)
            \App\Models\User::where('class_id', $class->id)->update(['class_id' => null]);

            // 2. Keluarkan Semua Siswa (Analogi: Kosongkan Murid)
            // Kita hapus data penempatan siswa di kelas ini
            \App\Models\RefStudentAcademicYear::where('class_id', $class->id)->delete();

            // 3. Hapus Data Keuangan (Analogi: Bakar Buku Kas)
            // Hapus Pemasukan
            \App\Models\SClassIncome::where('class_id', $class->id)->delete();
            // Hapus Pengeluaran
            \App\Models\SClassExpense::where('class_id', $class->id)->delete();
            
            // Catatan: Data Penyaluran (Disbursement) Pengelola yang terkait kelas ini 
            // akan otomatis terhapus (Cascade) atau menyesuaikan tergantung setting database,
            // tapi biasanya aman karena alokasinya menempel pada kelas/expense.

            // 4. Hapus Kelas (Analogi: Hancurkan Gedung)
            $class->delete();
        });

        return redirect()->route('superadmin.classes.index')->with('success', 'Kelas beserta seluruh data siswa dan keuangannya berhasil dihapus.');
    }

    public function show(RefClass $class)
    {
        // Ambil siswa di kelas ini (tahun ini) dan urutkan nama
        $students = $class->studentsForThisYear()->orderBy('full_name')->get();

        return view('superadmin.classes.show', compact('class', 'students'));
    }
}