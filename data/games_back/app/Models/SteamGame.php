<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SteamGame extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'name',
        'is_free',
        'detailed_description',
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'is_free' => 'bool',
    ];
}
