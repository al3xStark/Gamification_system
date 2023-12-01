<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\PartnerUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class PartnerUserController extends BaseController
{

    public $partnerUser;
    public $access;

    /**
     * @throws Exception
     */
    private function setUser($user_uid)
    {
        if (empty($userData = Redis::get($user_uid))) {
            throw new Exception('user not found');
        }

        $userData = json_decode($userData, true);

        Validator::make($userData, [
            'id' => 'required|integer|exists:event_partner_user,id',
            'access' => 'required|string|in:view,write',
        ])->validate();

        $this->partnerUser = PartnerUser::where(['id' => $userData['id']])->firstOrFail();
        $this->access = $userData['access'];
    }

    /**
     * @throws Exception
     */
    public function itemsAction($user_uid)
    {
        $this->setUser($user_uid);
        return response($this->partnerUser->getRoom($this->access));
    }

    /**
     * @throws Exception
     */
    public function statsAction($user_uid)
    {
        $this->setUser($user_uid);
        if ($this->access !== 'write') {
            return response([]);
        }
        return response($this->partnerUser->getStats());
    }

    /**
     * @throws Exception
     */
    public function achievementsAction($user_uid)
    {
        $this->setUser($user_uid);
        if ($this->access !== 'write') {
            return response([]);
        }
        return response($this->partnerUser->getAchievements());
    }

    /**
     * @throws Exception
     */
    public function setAchievementTemplateAction(Request $request, $user_uid)
    {
        $this->setUser($user_uid);

        if ($this->access !== 'write') {
            return response(false);
        }

        $request->validate([
            'achievementId' => 'required|integer'
        ]);

        $achievement = Achievement
            ::with('itemTemplate.item')
            ->findOrFail($request->achievementId);

        $roomItem = $achievement->itemTemplate->item;

        foreach (
            $roomItem
                ->roomItemTemplates()
                ->whereHas('partnerUsers', fn($query) => $query->where(['partner_user_id' => $this->partnerUser->id]))
                ->get() as $template
        ) {
            $template->partnerUsers()->detach($this->partnerUser->id);
        }

        if (!$achievement
            ->itemTemplate
            ->partnerUsers()
            ->where(['partner_users.id' => $this->partnerUser->id])
            ->first()) {
            $achievement->itemTemplate->partnerUsers()->attach($this->partnerUser->id);
        }

        return response(true);
    }
}
