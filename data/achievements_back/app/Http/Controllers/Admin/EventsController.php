<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventsController extends Controller
{
    public function getListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = Event::query();
        } else {
            $response = Event
                ::whereIn('project_id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response
            ->with('project')
            ->paginate(10);

        return response([
            'items' => $response->items(),
            'pagination' => [
                'currentPage' => $response->currentPage(),
                'perPage' => $response->perPage(),
                'total' => $response->total(),
            ]
        ]);
    }

    public function allowListAction($id): Response
    {
        return response(Event
            ::whereHas('project', fn($query) => $query->where('id', $id))
            ->get()
            ->toArray());
    }

    public function getAction(int $id): Response
    {
        return response(
            Event::where(['id' => $id])->first()
        );
    }

    public function createAction(Request $request): Response
    {
        $request->validate([
            'active' => 'required|boolean',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'project_id' => 'required|integer|exists:projects,id',
            'fields' => 'string|max:255|nullable',
        ]);

        $events = Event::create([
            'active' => $request->active,
            'name' => $request->name,
            'code' => $request->code,
            'project_id' => $request->project_id,
            'fields' => $request->fields ? explode(',', $request->fields) : [],
        ]);

        return response($events);
    }

    public function updateAction(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer|exists:events,id',
            'active' => 'required|boolean',
            'name' => 'required|string|max:255',
        ]);

        $event = Event::findOrFail($request->id);

        $event->active = $request->active;
        $event->name = $request->name;

        $event->save();

        return response($event);
    }
}
