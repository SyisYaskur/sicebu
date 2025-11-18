<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Gunakan Model biasa, bukan Pivot

class RefStudentAcademicYear extends Model
{
    use HasFactory, HasUuids; // Pivot ini juga pakai UUID

    protected $table = 'ref_student_academic_years'; // Nama tabel pivot
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year',
        'created_by',
        'updated_by',
    ];

    // Relasi ke Siswa
    public function student()
    {
        return $this->belongsTo(RefStudent::class, 'student_id');
    }

    // Relasi ke Kelas
    public function class() // Gunakan nama 'classRoom' jika 'class' bentrok
    {
        return $this->belongsTo(RefClass::class, 'class_id');
    }
}