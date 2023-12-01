<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reward extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement',
    ];
}
