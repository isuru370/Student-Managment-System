<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraIncomes extends Model
{
    use HasFactory;

    protected $table = "extra_incomes";

    protected $fillable = [
        'reason',
        'amount',
    ];

    // Type casting for JSON responses
    protected $casts = [
        'amount'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
