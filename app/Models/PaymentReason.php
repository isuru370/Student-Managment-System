<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReason extends Model
{
    use HasFactory;

    protected $table = "payment_reason";

    protected $fillable = [
        'reason_code',
        'reason',
    ];

    // Type casting for JSON responses
    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
