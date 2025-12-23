<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassCategoryHasStudentClass extends Model
{
    use HasFactory;

    protected $table = 'class_category_has_student_class';

    protected $fillable = [
        'fees',
        'student_classes_id',
        'class_category_id',
    ];

    protected $casts = [
        'id'                 => 'integer',

        // Money
        'fees'               => 'float',

        // Foreign keys
        'student_classes_id' => 'integer',
        'class_category_id'  => 'integer',

        // Timestamps
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    // Relationship: belongs to StudentClass
    public function studentClass()
    {
        return $this->belongsTo(ClassRoom::class, 'student_classes_id');
    }

    // Relationship: belongs to ClassCategory
    public function classCategory()
    {
        return $this->belongsTo(ClassCategory::class, 'class_category_id');
    }
}
