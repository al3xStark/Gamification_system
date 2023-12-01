<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Game;
use App\Models\Reward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GamesController extends Controller
{
    public function autocompleteListAction($mask = ''): Response
    {
        $response = Game
            ::where('name', 'LIKE', '%' . $mask . '%')
            ->limit(10)
            ->orderBy('rating', 'desc')
            ->get();

        return response($response->toArray());
    }

    public function setRatingAction($id = 0, Request $request): Response
    {
        $request->validate([
            'rate' => 'required|integer|in:1,2,3,4,5',
        ]);

        return response([$id, $request->rate]);
    }

    /**
     * @throws Exception
     */
    public function getMyGamesAction(): Response
    {
        // todo get my games
        return response(Game::limit(10)->get());
    }

    /**
     * @throws Exception
     */
    public function getMyAchievementsAction(): Response
    {
        $items = Reward::where([
            'user_id' => auth()->user()->id,
        ])->get()->pluck('achievement');

        Reward::where([
            'user_id' => auth()->user()->id,
        ])->delete();

        return response($items);
    }

    /**
     * @throws Exception
     */
    public function getAction($id = 0): Response
    {
        $response = Game
            ::where([
                'id' => $id,
            ])
            ->first();

        if (!$response) {
            throw new Exception('Game not found');
        }

        $data = [
            "project_id" => env('ACHIEVEMENTS_PROJECT_ID', false),
            "user_id" => auth()->user()->id,
            "code" => "view_game",
        ];

        ksort($data);
        $sign = hash('sha256', urldecode(http_build_query($data)) . env('ACHIEVEMENTS_KEY'));

        if (auth()->user()) {
            Http::withHeaders([
                'signature' => $sign
            ])->post(env('ACHIEVEMENTS_SERVICE_URL') . '/api/event/create/', $data);
        }

        return response($response->toArray());
    }

    /**
     * @throws Exception
     */
    public function commentsAction($id = 0): Response
    {
        $game = Game::with('comments.user')->findOrFail($id);
        return response($game->comments);
    }

    /**
     * @throws Exception
     */
    public function setCommentAction(Request $request, $id = 0): Response
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $game = Game::findOrFail($id);

        $data = [
            "project_id" => env('ACHIEVEMENTS_PROJECT_ID', false),
            "user_id" => auth()->user()->id,
            "code" => "add_comment",
        ];

        ksort($data);
        $sign = hash('sha256', urldecode(http_build_query($data)) . env('ACHIEVEMENTS_KEY'));

        Http::withHeaders([
            'signature' => $sign
        ])->post(env('ACHIEVEMENTS_SERVICE_URL') . '/api/event/create/', $data);

        return response(
            Comment::create([
                'user_id' => auth()->user()->id,
                'game_id' => $game->id,
                'comment' => $request->comment,
            ])
        );
    }
}
