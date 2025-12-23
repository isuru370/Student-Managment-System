<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassHalls extends Model
{
    use HasFactory;

    protected $table = 'class_halls';

    protected $fillable = [
        'hall_id',
        'hall_name',
        'hall_type',
        'hall_price',
        'status',
    ];

    protected $casts = [
        'id'         => 'integer',

        // Prices
        'hall_price' => 'float',

        // Status
        'status'     => 'boolean',

        // Timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
