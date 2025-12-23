<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStudentStudentClass extends Model
{
    use HasFactory;

    protected $table = 'student_student_student_classes';

    protected $fillable = [
        'student_id',
        'student_classes_id',
        'class_category_has_student_class_id',
        'status',
        'is_free_card'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'student_id'                       => 'integer',
        'student_classes_id'               => 'integer',
        'class_category_has_student_class_id' => 'integer',
        'status'                           => 'boolean',
        'is_free_card'                     => 'boolean',
        'created_at'                       => 'datetime',
        'updated_at'                       => 'datetime',
    ];

    // Student relationship
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // Class Room relationship
    public function studentClass()
    {
        return $this->belongsTo(ClassRoom::class, 'student_classes_id', 'id');
    }

    // Class Category relationship
    public function classCategoryHasStudentClass()
    {
        return $this->belongsTo(ClassCategoryHasStudentClass::class, 'class_category_has_student_class_id', 'id');
    }
}
