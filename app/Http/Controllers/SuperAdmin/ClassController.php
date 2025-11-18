<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\RefClass;
use App\Models\RefStudent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoreExpertiseProgram; // Tambahkan model
use App\Models\CoreExpertiseConcentration; // Tambahkan model
use Illuminate\Support\Facades\Auth; // Untuk created_by/updated_by

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data kelas beserta relasi (jika ada) dan paginate
        $classes = RefClass::with(['expertiseProgram', 'expertiseConcentration'])
                           ->latest()
                           ->paginate(10);
        return view('superadmin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = CoreExpertiseProgram::orderBy('name')->get();
        $concentrations = CoreExpertiseConcentration::orderBy('name')->get();
        return view('superadmin.classes.create', compact('programs', 'concentrations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'nip_number' => 'nullable|string|max:255',
            'nuptk_number' => 'nullable|string|max:255',
            'academic_level' => 'required|integer|min:10|max:13', // Asumsi level 10-13
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'], // Format YYYY/YYYY
            'expertise_program_id' => 'nullable|string|exists:core_expertise_programs,id',
            'expertise_concentration_id' => 'nullable|string|exists:core_expertise_concentrations,id',
        ],[
            'academic_year.regex' => 'Format Tahun Akademik harus YYYY/YYYY (contoh: 2025/2026)',
        ]);

        // Tambahkan created_by
        $validatedData['created_by'] = Auth::id();

        RefClass::create($validatedData);

        return redirect()->route('superadmin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

   public function show(RefClass $class)
    {
        // Eager load relasi yang dibutuhkan di view
        $class->load(['expertiseProgram', 'expertiseConcentration']);

        // Ambil siswa hanya untuk tahun ajaran kelas ini
        $students = $class->studentsForThisYear()->orderBy('full_name')->get();

        return view('superadmin.classes.show', compact('class', 'students'));
    }

    public function edit(RefClass $class)
    {
        $programs = CoreExpertiseProgram::orderBy('name')->get();
        $concentrations = CoreExpertiseConcentration::orderBy('name')->get();

        // Ambil ID siswa yang sudah ada di kelas ini & tahun ajaran ini
        $assignedStudentIds = $class->studentsForThisYear()->pluck('ref_students.id')->toArray();

        // Ambil SEMUA siswa (untuk pilihan). Pertimbangkan performa jika > 1000 siswa.
        // Mungkin lebih baik ambil siswa yang belum punya kelas di tahun ajaran ini? (Query lebih kompleks)
        // Untuk sekarang, ambil semua dulu.
        $allStudents = RefStudent::orderBy('full_name')->get(['id', 'full_name', 'student_number', 'national_student_number']); // Ambil kolom yg perlu saja

        return view('superadmin.classes.edit', compact('class', 'programs', 'concentrations', 'allStudents', 'assignedStudentIds'));
    }

    public function update(Request $request, RefClass $class)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'nip_number' => 'nullable|string|max:255',
            'nuptk_number' => 'nullable|string|max:255',
            'academic_level' => 'required|integer|min:10|max:13',
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'expertise_program_id' => 'nullable|string|exists:core_expertise_programs,id',
            'expertise_concentration_id' => 'nullable|string|exists:core_expertise_concentrations,id',
            'students' => 'nullable|array', // Validasi input students
            'students.*' => 'string|exists:ref_students,id' // Validasi setiap ID siswa
        ],[
            'academic_year.regex' => 'Format Tahun Akademik harus YYYY/YYYY (contoh: 2025/2026)',
        ]);

        // Pisahkan data siswa dari data kelas
        $studentIds = $request->input('students', []); // Ambil ID siswa yg dipilih, default array kosong
        unset($validatedData['students']); // Hapus dari data validasi kelas

        $validatedData['updated_by'] = Auth::id();

        // 1. Update data kelas
        $class->update($validatedData);

        // 2. Sync siswa ke tabel pivot (ref_student_academic_years)
        // Siapkan data untuk sync, tambahkan academic_year dari kelas yang diupdate
        $syncData = [];
        foreach ($studentIds as $studentId) {
            $syncData[$studentId] = ['academic_year' => $class->academic_year];
        }

        // Gunakan relasi students() untuk sync, Laravel akan menangani attach/detach
        // PENTING: Sync ini akan mempengaruhi SEMUA entri di pivot untuk kelas ini.
        // Jika ingin hanya mempengaruhi tahun ajaran tertentu, query manual insert/delete lebih aman.
        // TAPI, karena kita edit kelas untuk tahun ajaran tertentu, sync() seharusnya OK.
        $class->students()->sync($syncData);

        // Alternatif (lebih aman jika khawatir sync menimpa tahun lain):
        // a. Hapus semua entri di pivot untuk class_id & academic_year ini
        // RefStudentAcademicYear::where('class_id', $class->id)->where('academic_year', $class->academic_year)->delete();
        // b. Buat entri baru berdasarkan $studentIds
        // foreach ($studentIds as $studentId) {
        //     RefStudentAcademicYear::create([
        //         'student_id' => $studentId,
        //         'class_id' => $class->id,
        //         'academic_year' => $class->academic_year,
        //         'created_by' => Auth::id(), // Jika ada
        //     ]);
        // }
        // Pilih salah satu metode (sync() lebih simpel)

        return redirect()->route('superadmin.classes.index')->with('success', 'Kelas dan daftar siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RefClass $class)
    {
         try {
            $class->delete();
            return redirect()->route('superadmin.classes.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            // Tangani jika ada error (misal karena relasi ke siswa)
            return redirect()->route('superadmin.classes.index')->with('error', 'Gagal menghapus kelas. Kemungkinan masih ada data siswa terkait. Error: ' . $e->getMessage());
        }
    }
}