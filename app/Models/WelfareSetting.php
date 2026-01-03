<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareSetting extends Model
{
    use HasFactory;

    protected $table = 'welfare_settings';

    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'decimal:2',
        'status' => 'integer',
    ];

    /**
     * User relationship
     * Welfare setting record එක system user (admin/staff) එකක් record කරයි
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
