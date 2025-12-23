<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'teachers';

    protected $fillable = [
        'custom_id',
        'fname',
        'lname',
        'email',
        'mobile',
        'nic',
        'bday',
        'gender',
        'address1',
        'address2',
        'address3',
        'is_active',
        'graduation_details',
        'experience',
        'precentage', // column name unchanged
        'account_number',
        'bank_branch_id',
    ];

    // Type casting for JSON responses
    protected $casts = [
        'is_active'     => 'boolean',
        'bank_branch_id'=> 'integer',
        'precentage'    => 'double',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function bankBranch()
    {
        return $this->belongsTo(BankBranch::class);
    }
}
