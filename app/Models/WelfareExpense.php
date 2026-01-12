<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WelfareExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'welfare_expenses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'receipt_no',
        'expense_for',
        'expense_type',
        'amount',
        'expense_date',
        'payment_method',
        'recorded_by',
        'description',
        'remarks',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'status' => 'integer',
    ];

    /**
     * Relationship: User who recorded the expense
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
