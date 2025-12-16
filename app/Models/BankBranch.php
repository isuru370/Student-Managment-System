<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankBranch extends Model
{
    use HasFactory;

    // DB table එක නිවැරදිව සඳහන් කරන්න
    protected $table = 'bank_branch';

    protected $fillable = [
        'bank_id',
        'branch_name',
        'branch_code'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}
