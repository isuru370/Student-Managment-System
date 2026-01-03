<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WelfarePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'welfare_payments';

    // Mass assignable fields
    protected $fillable = [
        'teacher_id',
        'user_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'description',
    ];

    // Type casting
    protected $casts = [
        'teacher_id' => 'integer',
        'user_id' => 'integer',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * Teacher relationship
     * Welfare payment එකක් teacher account එකකට අදාලයි
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * User relationship
     * Payment record එක system user (admin/staff) එකක් record කරයි
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
