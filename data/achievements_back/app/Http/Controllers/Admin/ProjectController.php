<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SentProjectKey;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    public function getListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = Project
                ::withCount('users');
        } else {
            $response = Project
                ::whereIn('id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response->paginate(10);

        return response([
            'items' => $response->items(),
            'pagination' => [
                'currentPage' => $response->currentPage(),
                'perPage' => $response->perPage(),
                'total' => $response->total(),
            ]
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getApiKeyAction(Request $request): Response
    {
        $request->validate([
            'project_id' => 'required|integer',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $user = User::where([
            'name' => $request->login
        ])->firstOrFail();

        if (auth()->user()->id !== $user->id) {
            throw new \Exception('Incorrect user');
        }

        if (!Hash::check($request->password, $user->password)) {
            throw new \Exception('Params incorrect');
        }

        $projectUser = $user->projectsUser()->where(['project_id' => $request->project_id])->firstOrFail();

        Mail::to(auth()->user()->email)->send(new SentProjectKey($projectUser->project));

        return response('success');
    }

    public function allowListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = Project::query();
        } else {
            $response = Project
                ::whereIn('id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response->get()->pluck('name', 'id');

        return response($response);
    }

    public function getAction(int $id): Response
    {

        $result = Project
            ::where(['id' => $id])
            ->with('roomItems', fn($query) => $query->where('active', true))
            ->first();

        $items = [];

        foreach ($result->roomItems as $roomItem) {
            $items[] = $roomItem->prePareforUser();
        }

        return response(
            array_merge($result->toArray(),['room_items' => $items])
        );
    }

    public function createAction(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'callback_url' => 'url',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'callback_url' => $request->callback_url ?? '',
        ]);

        $user = User::where('id', auth()->user()->id)->firstOrFail();

        $user->projectsUser()->where('user_id', auth()->user()->id)->create([
            'project_id' => $project->id,
        ]);

        $user->refresh();

        foreach ($user->projectsUser()->where([
            'user_id' => auth()->user()->id,
            'project_id' => $project->id,
        ])->get() as $projectUser) {
            $projectUser->groups()->attach(Group::where('name', 'ProjectAdmin')->first()->id);
        }

        return response($project);
    }

    public function updateAction(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer|exists:projects,id',
            'name' => 'string|max:255',
            'callback_url' => 'url',
        ]);

        $project = Project::findOrFail($request->id);

        if ($request->name) {
            $project->name = $request->name;
        }

        if ($request->callback_url) {
            $project->callback_url = $request->callback_url;
        }

        $project->save();

        return response($project);
    }
}
