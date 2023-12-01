<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function userBoardAction($userName = ''): Response
    {
        if (!empty($userName)) {
            $id = User::where(['name' => $userName])->firstOrFail()->id;
            $access = 'view';
        } else {
            $id = auth()->user()->id;
            $access = 'write';
        }

        $data = [
            "project_id" => env('ACHIEVEMENTS_PROJECT_ID', false),
        ];

        ksort($data);
        $sign = hash('sha256', urldecode(http_build_query($data)) . env('ACHIEVEMENTS_KEY'));

        return response(
            Http::withHeaders([
                'signature' => $sign
            ])->get(env('ACHIEVEMENTS_SERVICE_URL') . '/api/user-room-link/' . $id . '/' . $access . '?' . http_build_query($data)));
    }

    public function autocompleteListAction($mask = ''): Response
    {
        $response = User
            ::where('name', 'LIKE', '%' . $mask . '%')
            ->limit(10)
            ->get();

        return response($response->toArray());
    }
}
