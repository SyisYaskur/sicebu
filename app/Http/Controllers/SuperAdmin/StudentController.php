<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RefStudent;
use App\Models\RefClass;
use App\Models\RefStudentAcademicYear;
use App\Models\SClassIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RefStudent::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('national_student_number', 'like', "%{$search}%")
                  ->orWhere('national_identification_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('full_name')->paginate(15)->withQueryString();
        return view('superadmin.students.index', compact('students', 'search'));
    }

    public function create()
    {
        $currentYear = '2025/2026'; 
        $classes = RefClass::where('academic_year', $currentYear)->orderBy('name')->get();
        return view('superadmin.students.create', compact('classes', 'currentYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'student_number' => 'nullable|string|unique:ref_students,student_number',
            'national_student_number' => 'nullable|string|unique:ref_students,national_student_number',
            'national_identification_number' => 'nullable|string|unique:ref_students,national_identification_number',
            'gender' => 'required|in:Laki-Laki,Perempuan',
            'birth_place_date' => 'nullable|string',
            'religion' => 'nullable|string',
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'class_id' => 'nullable|exists:ref_classes,id',
        ]);

        $student = null;

        DB::transaction(function () use ($validated, $request, &$student) {
            // 1. Buat Siswa
            $student = RefStudent::create([
                'id' => (string) Str::uuid(),
                'full_name' => $validated['full_name'],
                'student_number' => $validated['student_number'] ?? null,
                'national_student_number' => $validated['national_student_number'] ?? null,
                'national_identification_number' => $validated['national_identification_number'] ?? null,
                'gender' => $validated['gender'],
                'birth_place_date' => $validated['birth_place_date'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'address' => $validated['address'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // 2. Masukkan ke Kelas
            if ($request->class_id) {
                $class = RefClass::find($request->class_id);
                RefStudentAcademicYear::create([
                    'id' => (string) Str::uuid(),
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year' => $class->academic_year,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        // REVISI 1: Redirect ke halaman SHOW (Detail Siswa)
        return redirect()->route('superadmin.students.show', $student->id)
                         ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(RefStudent $student)
    {
        $classHistory = $student->classAssignments()
                                ->with('classRoom')
                                ->orderByDesc('academic_year')
                                ->get();
        return view('superadmin.students.show', compact('student', 'classHistory'));
    }

    public function edit(RefStudent $student)
    {
        $currentYear = '2025/2026';
        $currentClassAssign = $student->classAssignments()
                                      ->where('academic_year', $currentYear)
                                      ->first();
        $classes = RefClass::where('academic_year', $currentYear)->orderBy('name')->get();

        return view('superadmin.students.edit', compact('student', 'classes', 'currentClassAssign', 'currentYear'));
    }

   public function update(Request $request, RefStudent $student)
    {
        // 1. Validasi Lengkap
        $validated = $request->validate([
            // Identitas
            'full_name' => 'required|string|max:255',
            'student_number' => ['nullable', Rule::unique('ref_students')->ignore($student->id)],
            'national_student_number' => ['nullable', Rule::unique('ref_students')->ignore($student->id)],
            'national_identification_number' => ['nullable', Rule::unique('ref_students')->ignore($student->id)],
            'gender' => 'required|in:Laki-Laki,Perempuan',
            'birth_place_date' => 'nullable|string',
            'religion' => 'nullable|string',
            'address' => 'nullable|string',
            'child_status' => 'nullable|string',
            'birth_order' => 'nullable|string',
            'siblings' => 'nullable|integer',

            // Fisik & Minat
            'blood_type' => 'nullable|string',
            'height_cm' => 'nullable|numeric',
            'weight_kg' => 'nullable|numeric',
            'hobby' => 'nullable|string',
            'aspiration' => 'nullable|string',

            // Orang Tua (Ayah)
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_education' => 'nullable|string',
            'guardian_occupation' => 'nullable|string',
            'guardian_income' => 'nullable|numeric',

            // Ibu
            'mother_name' => 'nullable|string',
            'mother_phone' => 'nullable|string',
            'mother_education' => 'nullable|string',
            'mother_occupation' => 'nullable|string',
            'mother_income' => 'nullable|numeric',

            // Wali (Custodian)
            'custodian_name' => 'nullable|string',
            'custodian_phone' => 'nullable|string',
            'custodian_education' => 'nullable|string',
            'custodian_occupation' => 'nullable|string',

            // Kelas
            'class_id' => 'nullable|exists:ref_classes,id',
        ]);

        DB::transaction(function () use ($validated, $request, $student) {
            // 2. Update Data Siswa (Semua field selain class_id)
            // array_diff_key membuang 'class_id' dari array data siswa
            $student->update(array_diff_key($validated, ['class_id' => '']));

            // 3. Update Perpindahan Kelas
            $currentYear = '2025/2026'; // Sebaiknya dinamis
            
            if ($request->filled('class_id')) {
                RefStudentAcademicYear::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'academic_year' => $currentYear
                    ],
                    [
                        'id' => (string) Str::uuid(), // Generate UUID jika create baru
                        'class_id' => $request->class_id,
                        'updated_by' => Auth::id()
                    ]
                );
            } else {
                // Jika dikosongkan, hapus dari kelas tahun ini
                RefStudentAcademicYear::where('student_id', $student->id)
                                      ->where('academic_year', $currentYear)
                                      ->delete();
            }
        });

        // Redirect ke SHOW sesuai request Anda
        return redirect()->route('superadmin.students.show', $student->id)
                         ->with('success', 'Data lengkap siswa berhasil diperbarui.');
    }

    public function destroy(RefStudent $student)
    {
        DB::transaction(function () use ($student) {
            $student->classAssignments()->delete();
            $student->delete();
        });

        return redirect()->route('superadmin.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}