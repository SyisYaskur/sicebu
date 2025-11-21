<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SDisbursement extends Model
{
    use HasUuids;

    protected $table = 's_disbursements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'purpose',
        'total_amount',
        'disbursement_date',
        'notes',
        'proof_image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'disbursement_date' => 'date',
    ];

    public function allocations()
    {
        return $this->hasMany(SFundAllocation::class, 'disbursement_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}