<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules;


class AuthController extends Controller
{
    public function signinAction(Request $request)
    {
        if (auth()->attempt($request->all())) {
            return response($this->getUserInfo(auth()->user()), Response::HTTP_OK);
        }

        return response([
            'message' => 'This User does not exist'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @throws Exception
     */
    public function sendResetLinkEmailAction(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::createToken(User::where('email', $request->email)->firstOrFail());

        return ($response && Mail::to($request->email)->send(new ResetPassword($response)))
            ? response('success', 200)
            : response('error', 422);
    }

    public function resetPasswordAction(Request $request)
    {
        $request->validate([
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return ($status === Password::PASSWORD_RESET)
            ? response($status, 200)
            : response(['token' => $request->code, Crypt::encrypt($request->code)], 422);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function signupAction(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'active' => true,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $project = Project::create([
            'name' => $request->name,
        ]);

        $user->projectsUser()->where('user_id', $user->id)->create([
            'project_id' => $project->id,
        ]);

        $user->refresh();

        foreach ($user->projectsUser()->where([
            'user_id' => auth()->user()->id,
            'project_id' => $project->id,
        ])->get() as $projectUser) {
            $projectUser->groups()->attach(Group::where('name', 'ProjectAdmin')->first()->id);
        }

        $user->refresh();
        return response($this->getUserInfo($user));
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserInfo($user)
    {
        return [
            'user' => $this->getUserData($user),
            'groups' => $user->getGroupNames(),
            'projects' => $this->getUserProjects($user),
            'access_token' => $user->createToken('authToken', $this->getScopes($user))->accessToken
        ];
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserData($user): array
    {
        return [
            'name' => $user->name,
        ];
    }

    /**
     * @param $user
     * @return array
     */
    private function getScopes($user): array
    {
        if ($user->isSuperUser()) {
            return ['superuser'];
        }

        if ($user->isProjectAdmin()) {
            return ['project_admin'];
        }

        if ($user->isProjectManager()) {
            return ['project_manager'];
        }

        return [];
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserProjects($user)
    {
        $projects = [];
        $projectsUser = $user->projectsUser;

        foreach ($projectsUser as $projectUser) {
            $projects[] = (array_merge($projectUser->project->toArray(), ['group' => array_column($projectUser->groups->toArray(), 'name')]));
        }

        return $projects;
    }
}
