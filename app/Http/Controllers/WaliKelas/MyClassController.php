<?php

namespace App\Http\Controllers\WaliKelas; // Pastikan namespace benar

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RefClass;
use App\Models\RefStudent;
use App\Models\RefStudentAcademicYear; // Pivot table model
use Illuminate\Validation\Rule;

class MyClassController extends Controller
{


    /**
     * Menampilkan detail kelas dan daftar siswa wali kelas.
     */
    public function show()
    {
        $class = Auth::user()->getWaliKelasClass();

        if (!$class) {
            // Jika guru tidak punya kelas di tahun ajaran ini
            return view('walikelas.my_class.no_class'); // Buat view ini
        }

        // Ambil siswa di kelas ini untuk tahun ajaran ini
        $assignedStudents = $class->studentsForThisYear()
                                  ->orderBy('full_name')
                                  ->get();

        // Ambil siswa yang BELUM punya kelas di tahun ajaran ini
        $currentAcademicYear = $class->academic_year;
        $unassignedStudents = RefStudent::whereDoesntHave('classAssignments', function ($query) use ($currentAcademicYear) {
                                        $query->where('academic_year', $currentAcademicYear);
                                    })
                                    ->orderBy('full_name')
                                    ->get(['id', 'full_name', 'student_number', 'national_student_number']); // Ambil kolom yg perlu

        return view('walikelas.my_class.show', compact('class', 'assignedStudents', 'unassignedStudents'));
    }

    /**
     * Menambahkan siswa ke kelas wali kelas.
     */
    public function addStudent(Request $request)
    {
        $class = Auth::user()->getWaliKelasClass();
        if (!$class) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelas yang ditugaskan.');
        }

        $request->validate([
            'student_id' => [
                'required',
                'string',
                'exists:ref_students,id',
                // Pastikan siswa belum punya kelas di tahun ajaran ini
                Rule::unique('ref_student_academic_years')->where(function ($query) use ($class) {
                    return $query->where('academic_year', $class->academic_year);
                }),
            ],
        ],[
            'student_id.required' => 'Silakan pilih siswa yang akan ditambahkan.',
            'student_id.exists' => 'Siswa yang dipilih tidak valid.',
            'student_id.unique' => 'Siswa tersebut sudah terdaftar di kelas lain pada tahun ajaran ini.',
        ]);

        try {
            RefStudentAcademicYear::create([
                'student_id' => $request->student_id,
                'class_id' => $class->id,
                'academic_year' => $class->academic_year,
                'created_by' => Auth::id(), // Opsional
            ]);
            return redirect()->route('walikelas.my-class.show')->with('success', 'Siswa berhasil ditambahkan ke kelas.');
        } catch (\Exception $e) {
             return redirect()->route('walikelas.my-class.show')->with('error', 'Gagal menambahkan siswa. Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengeluarkan siswa dari kelas wali kelas.
     */
    public function removeStudent(RefStudent $student) // Terima RefStudent langsung
    {
         $class = Auth::user()->getWaliKelasClass();
        if (!$class) {
            return redirect()->back()->with('error', 'Anda tidak memiliki kelas yang ditugaskan.');
        }

        try {
            // Cari dan hapus entri pivot
            $assignment = RefStudentAcademicYear::where('student_id', $student->id)
                                                ->where('class_id', $class->id)
                                                ->where('academic_year', $class->academic_year)
                                                ->first();

            if ($assignment) {
                $assignment->delete();
                return redirect()->route('walikelas.my-class.show')->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
            } else {
                 return redirect()->route('walikelas.my-class.show')->with('error', 'Siswa tidak ditemukan di kelas ini untuk tahun ajaran ini.');
            }

        } catch (\Exception $e) {
            return redirect()->route('walikelas.my-class.show')->with('error', 'Gagal mengeluarkan siswa. Error: ' . $e->getMessage());
        }
    }
}