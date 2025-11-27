<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefStudent extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ref_students';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    /**
     * DAFTAR LENGKAP KOLOM YANG BISA DIEDIT
     */
    protected $fillable = [
        'id', 'user_id', 'created_by', 'updated_by',
        
        // Identitas Utama
        'full_name',
        'student_number', // NIS
        'national_student_number', // NISN
        'national_identification_number', // NIK
        'gender',
        'birth_place_date',
        'religion',
        'address',
        'child_status', // Anak kandung/tiri/angkat
        'birth_order', // Anak ke-
        'siblings', // Jumlah saudara

        // Data Fisik
        'blood_type',
        'height_cm',
        'weight_kg',
        
        // Minat
        'hobby',
        'aspiration',

        // Data Ayah / Wali
        'guardian_name',
        'guardian_education',
        'guardian_occupation',
        'guardian_income',
        'guardian_phone',

        // Data Ibu
        'mother_name',
        'mother_education',
        'mother_occupation',
        'mother_income',
        'mother_phone',

        // Data Wali (Custodian) - Jika tinggal dengan wali selain ortu
        'custodian_name',
        'custodian_phone',
        'custodian_occupation',
        'custodian_education',
    ];

    public function classAssignments()
    {
        return $this->hasMany(RefStudentAcademicYear::class, 'student_id');
    }
    
    public function classes()
    {
        return $this->belongsToMany(RefClass::class, 'ref_student_academic_years', 'student_id', 'class_id')
                    ->withPivot('academic_year')
                    ->withTimestamps();
    }
}