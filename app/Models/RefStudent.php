<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefStudent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ref_students'; // Nama tabel
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    // Sesuaikan $fillable dengan kolom yang relevan di tabel ref_students
    protected $fillable = [
        'user_id', 'no', 'student_id', 'student_number', 'national_student_number',
        'national_identification_number', 'full_name', 'birth_place_date', 'gender',
        'religion', /* ... tambahkan kolom lain jika perlu ... */ 'created_by', 'updated_by',
    ];

    /**
     * Relasi ke tabel pivot ref_student_academic_years
     * Menunjukkan kelas mana saja yang pernah diikuti siswa
     */
    public function classAssignments()
    {
        return $this->hasMany(RefStudentAcademicYear::class, 'student_id');
    }

     /**
     * Relasi Many-to-Many ke RefClass melalui pivot table
     */
    public function classes()
    {
        return $this->belongsToMany(RefClass::class, 'ref_student_academic_years', 'student_id', 'class_id')
                    ->withPivot('academic_year') // Penting untuk filter tahun ajaran
                    ->withTimestamps(); // Jika tabel pivot punya created_at/updated_at
    }
}