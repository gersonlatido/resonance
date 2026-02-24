<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['number', 'is_available'];

    protected $casts = [
        'is_available' => 'boolean',
    ];
}