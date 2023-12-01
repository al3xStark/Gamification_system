<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemTemplate;
use App\Models\Project;
use App\Models\RoomItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\File;

class ItemTemplateController extends Controller
{
    public function indexAction(): Response
    {
        return response(
            ItemTemplate
                ::with('item.defaultItem')
                ->whereHas('items', fn($query) => $query
                    ->where([
                        'room_items.project_id' => auth()->user()->projectsAllowedForAdministrationIds()
                    ]))
                ->get()
        );
    }

    public function allowListAction($id): Response
    {
        $response = Project::where([
            'id' => $id
        ])
            ->with('roomItems')
            ->first();

        $itemTemplates = ItemTemplate
            ::where(['active' => 1])
            ->whereHas('items', fn($query) => $query
                ->where([
                    'room_items.project_id' => auth()->user()->projectsAllowedForAdministrationIds()
                ])
                ->whereIn('room_items.id', $response->roomItems->pluck('id')))
            ->get()->toArray();

        return response($itemTemplates);
    }

    public function listForTemplateAction($templateId): Response
    {
        return response(
            ItemTemplate
                ::with('item.defaultItem')
                ->whereHas('items', fn($query) => $query
                    ->where([
                        'room_items.project_id' => auth()->user()->projectsAllowedForAdministrationIds()
                    ])
                    ->where(['room_items.id' => $templateId]))
                ->get()
        );
    }

    public function getAction($id): Response
    {
        if (empty($id)) {
            return $this->indexAction();
        }
        $template = ItemTemplate::with('items')->findOrFail($id);
        $items = $template->items->pluck('id');
        return response(array_merge($template->toArray(), ['items' => $items]));
    }

    public function createAction(Request $request): Response
    {
        $params = $request->validate([
            'active' => 'required|boolean',
            'name' => 'required|string|max:255',
        ]);

        $request->validate([
            'room_item_id' => 'required|integer|exists:room_items,id',
            'file' => [
                File::image()
                    ->max(12 * 256),
                'required'
            ]
        ]);

        $path = $request->file->store('templates');
        $params['template'] = asset('storage/' . $path);

        $roomItem = RoomItem
            ::whereIn('project_id', auth()->user()->projectsAllowedForAdministrationIds())
            ->where(['id' => $request->room_item_id])
            ->firstOrFail();
        $roomItemTemplate = ItemTemplate::create($params);

        $roomItem->roomItemTemplates()->attach($roomItemTemplate->id);

        return response($roomItemTemplate);
    }

    public function updateAction(Request $request): Response
    {
        $params = $request->validate([
            'active' => 'required|boolean',
            'name' => 'required|string|max:255',
        ]);

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

        $itemTemplate = ItemTemplate::findOrFail($request->id);

        $itemTemplate->update($params);

        $itemTemplate->refresh();

        return response($itemTemplate);
    }
}
