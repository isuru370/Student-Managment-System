<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallFee extends Model
{
    use HasFactory;

    // Table name (optional if Laravel naming convention followed)
    protected $table = 'hall_fees';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hall_id',
        'amount',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * Relationship: HallFee belongs to a ClassHall
     */
    public function hall()
    {
        return $this->belongsTo(ClassHalls::class, 'hall_id');
    }

    /**
     * Scope: Active fees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
