<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'custom_id',
        'fname',
        'lname',
        'mobile',
        'email',
        'whatsapp_mobile',
        'nic',
        'bday',
        'gender',
        'address1',
        'address2',
        'address3',
        'guardian_fname',
        'guardian_lname',
        'guardian_nic',
        'guardian_mobile',
        'is_active',
        'img_url',
        'grade_id',
        'admission',
        'is_freecard',
        'student_school'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'grade_id'    => 'integer',
        'admission'   => 'boolean',
        'is_freecard' => 'boolean',
        'is_active'   => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Relationship: student belongs to grade
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
