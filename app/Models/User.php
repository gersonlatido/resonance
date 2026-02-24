<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ✅ primary key is employee_id
    protected $primaryKey = 'employee_id';

    // ✅ employee_id is like "EMP001" (not auto-increment integer)
    public $incrementing = false;

    // ✅ employee_id is string
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

        // ✅ Automatically generate employee_id e.g. EMP001, EMP002
        static::creating(function ($user) {
            if (empty($user->employee_id)) {

                // Get last employee_id (EMP###) and increment
                $last = self::orderBy('employee_id', 'desc')->value('employee_id'); // ex: EMP009

                $lastNumber = 0;
                if ($last && preg_match('/EMP(\d+)/', $last, $m)) {
                    $lastNumber = (int) $m[1];
                }

                $nextNumber = $lastNumber + 1;

                // EMP001 format
                $user->employee_id = 'EMP' . str_pad((string)$nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}