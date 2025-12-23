<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = "exam";

    protected $fillable = [
        "title",
        "date",
        "start_time",
        "end_time",
        "student_classes_id",
        "is_canceled"
    ];

    // Type casting for JSON responses
    protected $casts = [
        'student_classes_id' => 'integer',
        'is_canceled'        => 'boolean',
        // Timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function studentClasses()
    {
        return $this->belongsTo(ClassRoom::class, 'student_classes_id');
    }
}
