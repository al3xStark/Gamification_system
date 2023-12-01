<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function getListAction(): Response
    {
        $response = User
            ::with('groups:name')
            ->paginate(10);

        return response([
            'items' => array_map(fn($item) => array_merge(
                $item->toArray(),
                ['groups' => $item->groups()->pluck('name')->toArray()],
            ), $response->items()),
            'pagination' => [
                'currentPage' => $response->currentPage(),
                'perPage' => $response->perPage(),
                'total' => $response->total(),
            ]
        ]);
    }

    public function getAction($id): Response
    {
        return response(
            User::where(['id' => $id])->with('groups')->first()
        );
    }

    public function updateAction(int $id, Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer|exists:room_items,id',
            'active' => 'required|boolean',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);

        $user->active = $request->active;
        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return response($user);
    }
}
