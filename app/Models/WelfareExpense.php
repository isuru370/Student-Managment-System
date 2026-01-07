<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WelfareExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_no',
        'expense_for',
        'expense_type',
        'amount',
        'expense_date',
        'payment_method',
        'approved_by',
        'recorded_by',
        'description',
        'remarks',
        'status'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->receipt_no)) {
                $year = Carbon::now()->format('Y');

                $lastReceipt = static::where('receipt_no', 'like', "WEXP-{$year}-%")
                    ->orderBy('receipt_no', 'desc')
                    ->first();

                if ($lastReceipt) {
                    $parts = explode('-', $lastReceipt->receipt_no);
                    $lastNumber = (int) end($parts);
                    $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }

                $model->receipt_no = "WEXP-{$year}-{$nextNumber}";
            }
        });
    }


    // Relationships
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopePending($query)
    {
        return $query->where('status', 2);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 3);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 0);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    // Accessor for formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) ($this->amount ?? 0), 2);
    }

    // Accessor for formatted date
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->expense_date)->format('Y-m-d');
    }
}
