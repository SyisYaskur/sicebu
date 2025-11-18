<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids; // Tambahkan ini

    protected $table = 'core_roles';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    /**
     * REVISI: Tambahkan properti $fillable
     * Ini adalah daftar kolom yang diizinkan untuk diisi massal (via create())
     */
    protected $fillable = [
        'id',
        'name',
        'code',
        'status',
        'app_type',
        'created_by',
        'updated_by',
    ];
}