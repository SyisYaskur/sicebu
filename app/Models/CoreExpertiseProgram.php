<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Gunakan UUID
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreExpertiseProgram extends Model
{
    use HasFactory, HasUuids; // Aktifkan UUID

    protected $table = 'core_expertise_programs'; // Nama tabel yang benar
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi
    protected $fillable = [
        'name',
        'slug',
        'avatar',
        'created_by',
        'updated_by',
    ];

    /**
     * Relasi one-to-many ke RefClass (satu program punya banyak kelas)
     */
    public function classes()
    {
        return $this->hasMany(RefClass::class, 'expertise_program_id');
    }
}