<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendances extends Model
{
    use HasFactory;

    protected  $table = "student_attendances";

    protected $fillable = [
        'at_date',
        'student_student_student_classes',
        'student_id',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relationship
    public function studentStudentClass()
    {
        return $this->belongsTo(
            StudentStudentStudentClass::class,
            'student_student_student_classes'
        );
    }
}
