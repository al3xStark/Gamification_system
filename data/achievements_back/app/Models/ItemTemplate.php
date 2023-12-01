<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property boolean $active
 * @property-read RoomItem $items
 * @property-read array getItemsIdsAttribute
 */
class ItemTemplate extends BaseModel
{
    protected $table = 'item_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
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
    ];

    public function items(): HasManyThrough {
        return $this->hasManyThrough(
            RoomItem::class,
            RoomItemTemplate::class,
            'item_template_id',
            'id',
            'id',
            'room_item_id'
        );
    }

    public function item(): HasOneThrough {
        return $this->hasOneThrough(
            RoomItem::class,
            RoomItemTemplate::class,
            'item_template_id',
            'id',
            'id',
            'room_item_id'
        );
    }

    public function getItemsIdsAttribute()
    {
        return $this->items->pluck('id');
    }

    /**
     * @return BelongsToMany
     */
    public function partnerUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            PartnerUser::class,
        )->withTimestamps();
    }
}
