<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RefStudent;
use App\Models\RefClass; // Untuk dropdown kelas
use App\Models\RefStudentAcademicYear; // Untuk update pivot
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Untuk validasi unique saat update

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = RefStudent::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%") // Cari NIS
                  ->orWhere('national_student_number', 'like', "%{$search}%") // KEMBALIKAN: Cari NISN
                  ->orWhere('national_identification_number', 'like', "%{$search}%"); // Cari NIK
            });
        }

        // Anda mungkin perlu menyesuaikan tahun ajaran ini atau membuatnya dinamis
        $currentAcademicYear = '2025/2026'; // Ganti dengan cara dinamis jika perlu

        $students = $query->with(['classes' => function ($q) use ($currentAcademicYear) {
                            $q->where('ref_student_academic_years.academic_year', $currentAcademicYear);
                         }])
                         ->latest('created_at')
                         ->paginate(15)
                         ->withQueryString();

        return view('superadmin.students.index', compact('students', 'search', 'currentAcademicYear'));
    }

    /**
     * Show the form for creating a new resource. (Jika diperlukan)
     */
    public function create()
    {
        // $classes = RefClass::orderBy('name')->get(); // Ambil kelas jika perlu assign saat create
        // return view('superadmin.students.create', compact('classes'));
        // Untuk saat ini, kita fokus pada edit. Jika butuh create, uncomment dan buat viewnya.
         return redirect()->route('superadmin.students.index')->with('info', 'Penambahan siswa baru sebaiknya melalui proses impor data.');
    }

    /**
     * Store a newly created resource in storage. (Jika diperlukan)
     */
    public function store(Request $request)
    {
        // Logika untuk menyimpan siswa baru
        // $request->validate([...]);
        // RefStudent::create([...]);
        // RefStudentAcademicYear::create([...]); // Assign kelas jika ada
        // return redirect()->route('superadmin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource. (Halaman Detail - Opsional)
     */
    public function show(RefStudent $student)
    {
        // Sama seperti kelas, bisa redirect ke edit
         return redirect()->route('superadmin.students.edit', $student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RefStudent $student)
    {
        // Ambil SEMUA kelas untuk tahun ajaran TERKINI (untuk dropdown pindah kelas)
        // Sesuaikan cara mendapatkan tahun ajaran terkini
        $currentAcademicYear = '2025/2026'; // Ganti dengan cara dinamis
        $classes = RefClass::where('academic_year', $currentAcademicYear)->orderBy('name')->get();

        // Ambil data assignment kelas siswa saat ini untuk tahun ajaran ini
        $currentAssignment = RefStudentAcademicYear::where('student_id', $student->id)
                                                    ->where('academic_year', $currentAcademicYear)
                                                    ->first();

        return view('superadmin.students.edit', compact('student', 'classes', 'currentAssignment', 'currentAcademicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RefStudent $student)
    {
        // Sesuaikan validasi dengan kolom di ref_students yang ingin diedit
         $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'student_number' => ['nullable','string','max:255', Rule::unique('ref_students')->ignore($student->id)],
            'national_student_number' => ['nullable','string','max:255', Rule::unique('ref_students')->ignore($student->id)],
            'national_identification_number' => ['nullable','string','max:255', Rule::unique('ref_students')->ignore($student->id)],
            'gender' => 'nullable|string|in:Laki-Laki,Perempuan',
            'religion' => 'nullable|string|max:255',
            'birth_place_date' => 'nullable|string|max:255', // Mungkin perlu dipisah?
            'address' => 'nullable|string',
            // Tambahkan validasi kolom lain yang ingin diedit
            // Validasi untuk assignment kelas
            'academic_year' => 'required|string', // Tahun ajaran target
            'class_id' => 'nullable|string|exists:ref_classes,id', // Kelas baru (atau kosong jika dihapus)
        ]);

        // Pisahkan data assignment kelas
        $academicYear = $validatedData['academic_year'];
        $newClassId = $validatedData['class_id'];
        unset($validatedData['academic_year'], $validatedData['class_id']);

        // Tambahkan updated_by
        $validatedData['updated_by'] = Auth::id();

        // 1. Update data siswa di tabel ref_students
        $student->update($validatedData);

        // 2. Update assignment kelas di tabel ref_student_academic_years
        RefStudentAcademicYear::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year' => $academicYear, // Filter berdasarkan tahun ajaran
            ],
            [
                'class_id' => $newClassId, // Set kelas baru (bisa null jika dihapus)
                'updated_by' => Auth::id(), // Atau created_by jika baru
                // 'created_by' => Auth::id(), // Jika ingin set saat create saja
            ]
        );

        return redirect()->route('superadmin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RefStudent $student)
    {
         try {
            // Hapus juga assignment kelasnya (opsional, tergantung kebutuhan)
            RefStudentAcademicYear::where('student_id', $student->id)->delete();

            $student->delete();
            return redirect()->route('superadmin.students.index')->with('success', 'Siswa berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('superadmin.students.index')->with('error', 'Gagal menghapus siswa. Error: ' . $e->getMessage());
        }
    }
}