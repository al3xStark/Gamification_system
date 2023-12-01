<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function getRoom()
    {
        $project = Project
            ::where(['id' => $this->project_id])
            ->with('roomItems', fn($query) => $query->where('active', true))
            ->whereHas('roomItems', fn($query) => $query->where('active', true))
            ->firstOrFail();

        $items = [];

        foreach ($project->roomItems as $roomItem) {
            $items[] = $roomItem->prePareforUser($this->id);
        }

        return $items;
    }

    public function getStats()
    {
        $sql = Achievement
            ::where([
                'active' => true,
                'project_id' => $this->project_id,
            ])
            ->with('eventPartnerUserByEvent', fn($query) => $query->where(['partner_user_id' => $this->id]))
            ->with('eventPartnerUserByHash', fn($query) => $query->where(['partner_user_id' => $this->id]))
            ->whereHas('eventPartnerUserByEvent', fn($query) => $query->where(['partner_user_id' => $this->id]))
            ->whereHas('eventPartnerUserByHash', fn($query) => $query->where(['partner_user_id' => $this->id]));

        $result = [];

        foreach ($sql->get() as $item) {
            $progress = floor(($item->eventPartnerUser()->count / $item->count) * 100);

            if ($progress >= 100) {
                continue;
            }

            $result[] = [
                'achievement' => $item->name,
                'progress' => $progress,
            ];
        }

        return $result;
    }

    public function getAchievements()
    {
        return Achievement
            ::where(['project_id' => $this->project_id])
            ->with('partnerUsers', fn($query) => $query->where('partner_user_id', $this->id))
            ->whereHas('partnerUsers', fn($query) => $query->where('partner_user_id', $this->id))
            ->get()->map(function ($achievement) {
                return [
                    'id' => $achievement->id,
                    'achievement' => $achievement->name,
                ];
            });
    }
}
