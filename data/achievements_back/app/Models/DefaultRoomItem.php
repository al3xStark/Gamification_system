<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultRoomItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'object',
        'material',
        'template',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'laravel_through_key',
    ];
}
