<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CallbackController extends Controller
{

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function __construct(Request $request)
    {
        $params = [
            "signature" => $request->header('signature'),
        ];

        Validator::make($params, [
            'signature' => 'required|string',
        ])->validate();

        $data = $request->toArray();

        ksort($data);
        $sign = hash('sha256', urldecode(http_build_query($data)) . env('ACHIEVEMENTS_KEY'));

        if (!hash_equals($sign, $params['signature'])) {
            throw new Exception('Signature not valid!', 401);
        }
    }

    public function indexAction(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:newUserAchievement',
            'user_id' => 'required|int',
            'achievement' => 'required|string',
        ]);

        Reward::create([
            'user_id' => $request->user_id,
            'achievement' => $request->achievement,
        ]);
    }
}
