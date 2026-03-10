<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

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

 protected static function boot()
{
    parent::boot();

    static::creating(function ($user) {

        if (empty($user->employee_id)) {

            $last = self::selectRaw("MAX(CAST(SUBSTRING(employee_id,4) AS UNSIGNED)) as max_id")
                ->value('max_id');

            $nextNumber = $last ? $last + 1 : 1;

            $user->employee_id = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

    });
}
}