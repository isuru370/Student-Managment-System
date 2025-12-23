<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankBranch extends Model
{
    use HasFactory;

    protected $table = 'bank_branch';

    protected $fillable = [
        'bank_id',
        'branch_name',
        'branch_code',
    ];

    protected $casts = [
        'id'         => 'integer',
        'bank_id'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
