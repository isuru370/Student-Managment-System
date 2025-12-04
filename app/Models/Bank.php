<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'banks';
    protected $fillable = ['bank_name', 'bank_code'];

    public function branches()
    {
        return $this->hasMany(BankBranch::class);
    }
}
