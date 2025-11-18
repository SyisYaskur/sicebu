<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Gunakan UUID
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefClass extends Model
{
    use HasFactory, HasUuids; // Aktifkan UUID

    protected $table = 'ref_classes'; // Nama tabel yang benar
    public $incrementing = false; // Primary key bukan auto-increment
    protected $keyType = 'string'; // Tipe primary key adalah string (UUID)
    protected $primaryKey = 'id'; // Definisikan primary key

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'name',
        'teacher_name',
        'nip_number',
        'nuptk_number',
        'academic_level',
        'academic_year',
        'expertise_program_id',
        'expertise_concentration_id',
        'created_by', // Tambahkan jika perlu melacak siapa yang membuat
        'updated_by', // Tambahkan jika perlu melacak siapa yang mengupdate
    ];

    // Definisikan relasi jika diperlukan (misal ke program keahlian)
    public function expertiseProgram()
    {
        return $this->belongsTo(CoreExpertiseProgram::class, 'expertise_program_id');
    }

    public function expertiseConcentration()
    {
        return $this->belongsTo(CoreExpertiseConcentration::class, 'expertise_concentration_id');
    }

    // Pastikan model CoreExpertiseProgram dan CoreExpertiseConcentration juga sudah dibuat

    /**
     * Relasi ke tabel pivot ref_student_academic_years
     */
    public function studentAssignments()
    {
        return $this->hasMany(RefStudentAcademicYear::class, 'class_id');
    }

    /**
     * Relasi Many-to-Many ke RefStudent melalui pivot table
     */
    public function students()
    {
        return $this->belongsToMany(RefStudent::class, 'ref_student_academic_years', 'class_id', 'student_id')
                    ->withPivot('academic_year')
                    ->withTimestamps(); // Jika tabel pivot punya timestamps
    }

    /**
     * Helper untuk mendapatkan siswa HANYA untuk tahun ajaran kelas ini
     */
    public function studentsForThisYear()
    {
        return $this->belongsToMany(RefStudent::class, 'ref_student_academic_years', 'class_id', 'student_id')
                    ->wherePivot('academic_year', $this->academic_year); // Filter berdasarkan tahun ajaran kelas
    }

    public function incomes()
    {
        return $this->hasMany(SClassIncome::class, 'class_id');
    }

    public function expenses()
    {
        return $this->hasMany(SClassExpense::class, 'class_id');
    }

    public function getFullNameAttribute()
    {
        return $this->academic_level . ' ' . $this->name;
    }
}