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

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
