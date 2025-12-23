<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'student_classes';

    protected $fillable = [
        'class_name',
        'is_active',
        'is_ongoing',
        'teacher_id',
        'subject_id',
        'grade_id'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'is_active'   => 'boolean',
        'is_ongoing'  => 'boolean',
        'teacher_id'  => 'integer',
        'subject_id'  => 'integer',
        'grade_id'    => 'integer',
        // Timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // A class belongs to a teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // A class belongs to a subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    // A class belongs to a grade
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }
}
