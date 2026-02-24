<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';  // Set employee_id as the primary key

    protected $fillable = [
        'employee_id',
        'name',
        'username',
        'email',
        'password',
        'position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        // Automatically generate employee_id on creating a new user
        static::creating(function ($user) {
            if (empty($user->employee_id)) {
                $user->employee_id = 'EMP' . str_pad(User::max('id') + 1, 2, '0', STR_PAD_LEFT);
            }
        });
    }
}
