<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuickPhoto extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'quick_photo';

    protected $fillable = [
        'custom_id',
        'quick_img',
        'grade_id',
        'is_active'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'grade_id'   => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
