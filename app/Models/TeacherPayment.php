<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPayment extends Model
{
    use HasFactory;

    protected $table = "teacher_payment";

    protected $fillable = [
        'payment',
        'date',
        'reason',
        'reason_code',
        'payment_for',
        'status',
        'user_id',
        'teacher_id'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'payment'    => 'double',
        'status'     => 'boolean',
        'user_id'    => 'integer',
        'teacher_id' => 'integer',
        'date'       => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reasonDetail()
    {
        return $this->belongsTo(PaymentReason::class, 'reason_code', 'reason_code'); // foreignKey, ownerKey
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
