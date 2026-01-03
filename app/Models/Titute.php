<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titute extends Model
{
    use HasFactory;

    // Explicit table name (recommended since it's not pluralized normally)
    protected $table = 'titute';

    // Mass-assignable attributes
    protected $fillable = [
        'student_id',
        'class_category_has_student_class_id',
        'titute_for',
        'status',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'student_id' => 'integer',
        'class_category_has_student_class_id' => 'integer',
        'status' => 'boolean',
    ];

     public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function classCategoryHasStudentClass()
    {
        return $this->belongsTo(ClassCategoryHasStudentClass::class, 'class_category_has_student_class_id', 'id');
    }
}
