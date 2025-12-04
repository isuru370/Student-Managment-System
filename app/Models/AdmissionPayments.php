<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPayments extends Model
{
    use HasFactory;

    protected $table = 'admission_payments';

    protected $fillable = [
        'student_id',
        'amount',
        'admission_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class,'student_id');
    }

    public function admission()
    {
        return $this->belongsTo(Admissions::class,'admission_id');
    }
}
