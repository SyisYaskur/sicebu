<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SFundAllocation extends Model
{
    use HasUuids;

    protected $table = 's_fund_allocations';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'disbursement_id',
        'class_id',
        'amount_transferred',
        'class_expense_id',
        'balance_before',
        'balance_after',
    ];

    public function disbursement()
    {
        return $this->belongsTo(SDisbursement::class, 'disbursement_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(RefClass::class, 'class_id');
    }
}