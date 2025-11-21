<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Concerns\HasUuids;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class SClassExpense extends Model
    {
        use HasFactory, HasUuids;

        protected $table = 's_class_expenses';
        public $incrementing = false;
        protected $keyType = 'string';
        protected $primaryKey = 'id';

        protected $fillable = [
            'class_id',
            'expense_date',
            'amount',
            'description',
            'recipient',
            'student_id',
            'proof_image',
            'created_by',
            'updated_by',
        ];

        protected $casts = [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];

        public function classRoom()
        {
            return $this->belongsTo(RefClass::class, 'class_id');
        }

        public function creator()
        {
            return $this->belongsTo(User::class, 'created_by');
        }

        public function student()
        {
            return $this->belongsTo(RefStudent::class, 'student_id');
        }
        }