<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CoreEmployee extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core_employees';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    // Lindungi dari Mass Assignment
    protected $guarded = [];

    /**
     * Relasi kembali ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}