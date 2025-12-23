<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutePayment extends Model
{
    use HasFactory;

    protected $table = "institute_payment";

    protected $fillable = [
        'payment',
        'date',
        'reason',
        'reason_code',
        'status',
        'user_id'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'payment' => 'double',
        'status'  => 'boolean',
        'user_id' => 'integer',
        'date'    => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
