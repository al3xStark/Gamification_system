<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserItemTemplate extends Pivot
{
    protected $table = 'user_item_templates';

    protected $casts = [
        'params' => 'json'
    ];

    protected $attributes = [
        'params' => '{}',
    ];
}
