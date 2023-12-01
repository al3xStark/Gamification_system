<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\File;

class RoomItemController extends Controller
{
    public function getListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = RoomItem::query();
        } else {
            $response = RoomItem
                ::whereIn('project_id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response->with(['project','defaultItem'])->paginate(10);

        return response([
            'items' => $response->items(),
            'pagination' => [
                'currentPage' => $response->currentPage(),
                'perPage' => $response->perPage(),
                'total' => $response->total(),
            ]
        ]);
    }

    public function allowListForTemplateAction($id): Response
    {
        $response = RoomItem
            ::whereIn('project_id', auth()->user()->projectsAllowedForAdministrationIds());

        if (!empty($id)) {
            $response->where(['id' => $id,]);
        }

        return response($response->get()->map(fn($item) => [
            'name' => $item['name'],
            'id' => $item['id'],
        ]));
    }

    public function getAction(int $id): Response
    {
        return response(
            RoomItem
                ::whereIn('project_id', auth()->user()->projectsAllowedForAdministrationIds())
                ->where(['id' => $id])
                ->first()
        );
    }

    public function createAction(Request $request): Response
    {
        $params = $request->validate([
            'active' => 'required|bool',
            'name' => 'required|string|max:255',
            'project_id' => 'required|integer|exists:projects,id',
            'coordinates' => 'required|string',
            'rotation' => 'required|string',
            'default_room_item_id' => 'required|integer|exists:default_room_items,id',
            'link' => 'url|nullable',
        ]);

        $params['link'] = $params['link'] ?? '';

        $request->validate([
            'file' => [
                File::image()
                    ->max(12 * 256),
                'nullable'
            ]
        ]);

        if ($request->file) {
            $path = $request->file->store('templates');
            $params['template'] = asset('storage/' . $path);
        }

        $roomItem = RoomItem::create($params);

        return response($roomItem);
    }

    public function updateAction(Request $request): Response
    {
        $params = $request->validate([
            'active' => 'required|bool',
            'name' => 'required|string|max:255',
            'coordinates' => 'required|string',
            'rotation' => 'required|string',
            'link' => 'url|nullable',
        ]);

        $params['link'] = $params['link'] ?? '';

        $request->validate([
            'id' => 'required|integer|exists:room_items,id',
            'file' => [
                File::image()
                    ->max(12 * 256),
                'nullable'
            ]
        ]);

        if ($request->file) {
            $path = $request->file->store('templates');
            $params['template'] = asset('storage/' . $path);
        }

        $roomItem = RoomItem::findOrFail($request->id);

        $roomItem->update($params);

        $roomItem->refresh();

        return response($roomItem);
    }
}
