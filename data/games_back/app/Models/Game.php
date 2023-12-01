<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'rawg_id',
        'name',
        'description',
        'slug',
        'rating',
        'ratings_count',
        'background_image',
        'short_screenshots',
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'short_screenshots' => 'json'
    ];

    protected $attributes = [
        'short_screenshots' => '{}',
    ];

    /**
     * @return BelongsToMany
     */
    public function platform(): BelongsToMany
    {
        return $this->belongsToMany(
            Platform::class,
        )->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(
            Comment::class,
        );
    }
}
