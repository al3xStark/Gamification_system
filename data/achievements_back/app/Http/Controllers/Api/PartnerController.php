<?php

namespace App\Http\Controllers\Api;

use App\Models\PartnerUser;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PartnerController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function userRoomLinkAction($user_id, $access)
    {
        if (!in_array($access, ['view', 'write'])) {
            throw new \Exception('Unknown access value');
        }

        if (!($user = PartnerUser::where([
            'project_id' => auth()->id(),
            'user_id' => $user_id,
        ])->first())) {
            $user = PartnerUser::create([
                'project_id' => auth()->id(),
                'user_id' => $user_id,
            ]);
        }

        $token = Str::uuid()->toString();

        Redis::set(
            $token,
            json_encode([
                'id' => $user->id,
                'access' => ($access === 'write') ? 'write' : 'view',
            ])
        );

        return response(env('SITE_URL') . '/user-room/' . $token);
    }
}
