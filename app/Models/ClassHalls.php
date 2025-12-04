<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassHalls extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'class_halls';

    // Fillable columns
    protected $fillable = [
        'hall_id',
        'hall_name',
        'hall_type',
        'hall_price',
        'status',
    ];
}
