<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    use HasFactory;

    protected $table = 'class_attendances';
    protected $appends = ['classAttendanceId'];

    protected $fillable = [
        'start',
        'end',
        'status',
        'class_category_has_student_class_id',
        'start_time',
        'end_time',
        'day_of_week',
        'is_ongoing',
        'class_hall_id',
        'date'
    ];

    public function getClassAttendanceIdAttribute()
    {
        return $this->attributes['id'];
    }

    public function classCategoryStudentClass()
    {
        return $this->belongsTo(ClassCategoryHasStudentClass::class, 'class_category_has_student_class_id');
    }

    public function hall() // renamed for convenience
    {
        return $this->belongsTo(ClassHalls::class, 'class_hall_id');
    }

    protected $casts = [
        'date' => 'date',
        'status' => 'integer'
    ];
}
