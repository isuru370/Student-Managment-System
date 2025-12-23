<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    // Type casting for JSON responses
    protected $casts = [
        'user_type'   => 'integer',
        'is_active'   => 'boolean',
        'email_verified_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function systemUser()
    {
        return $this->hasOne(SystemUser::class, 'user_id');
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type', 'id');
    }
}
