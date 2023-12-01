<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property boolean active
 * @property string created_at
 * @property string updated_at
 * @property int default_room_item_id
 * @property string name
 * @property int project_id
 * @property string coordinates
 * @property string rotation
 * @property string template
 */
class RoomItem extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'default_room_item_id',
        'name',
        'project_id',
        'coordinates',
        'rotation',
        'template',
        'link',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'laravel_through_key'
    ];

    protected $casts = [
        'active' => 'bool',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($item) {
            if (empty($item->template)) {
                $item->template = DefaultRoomItem::findOrFail($item->default_room_item_id)->template;
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
        );
    }

    /**
     * @return BelongsToMany
     */
    public function roomItemTemplates(): BelongsToMany
    {
        return $this->belongsToMany(
            ItemTemplate::class,
            'room_item_item_template',
        )->withTimestamps();
    }

    public function roomItemTemplatesIds()
    {
        return $this->roomItemTemplates->pluck('id');
    }

    public function defaultItem(): HasOne
    {
        return $this->hasOne(
            DefaultRoomItem::class,
            'id',
            'default_room_item_id'
        );
    }

    public function prePareforUser($userId = 0)
    {

        if ($userId) {
            $customTemplate = $this
                ->roomItemTemplates()
                ->whereHas('partnerUsers', fn($query) => $query->where([
                    'partner_user_id' => $userId
                ]))
                ->latest()
                ->first();

            if (!is_null($customTemplate)) {
                $customTemplate = $customTemplate->template;
            }

        }

        [$cx, $cy, $cz] = explode(',', $this->coordinates);
        [$rx, $ry, $rz] = explode(',', $this->rotation);

        return [
            'coordinates' => [
                'x' => $cx,
                'y' => $cy,
                'z' => $cz,
            ],
            'rotation' => [
                'x' => $rx,
                'y' => $ry,
                'z' => $rz,
            ],
            'object' => $this->defaultItem->object,
            'material' => $this->defaultItem->material,
            'template' => $customTemplate ?? $this->template,
            'link' => $this->link ?? '',
        ];
    }
}
