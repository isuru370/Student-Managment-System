<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemUser extends Model
{
    use HasFactory;

    protected $table = 'system_users';

    protected $fillable = [
        'custom_id',
        'user_id',
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
        'is_active'
    ];

    // Type casting for JSON responses
    protected $casts = [
        'user_id'    => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // SystemUser belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Through User, we can get UserType
    public function userType()
    {
        return $this->hasOneThrough(
            UserType::class,
            User::class,
            'id', // Foreign key on users table
            'id', // Foreign key on user_types table
            'user_id', // Local key on system_users
            'user_type' // Local key on users
        );
    }
}
