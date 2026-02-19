<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'customer_name',
        'table_number',
        'rating',
        'comment',
        'is_reviewed',
    ];
}
