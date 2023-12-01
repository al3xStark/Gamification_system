<?php

namespace App\Providers;

use App\Models\Project;
use Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensCan([
            'superuser' => 'Can all actions',
            'project_admin' => 'Can do all actions for project',
            'project_manager' => 'Can do same actions for project',
        ]);

        Auth::viaRequest('api-key', function (Request $request) {
            $params = [
                "project_id" => $request->get('project_id', null),
                "signature" => $request->header('signature'),
            ];

            Validator::make($params, [
                'project_id' => 'required|integer',
                'signature' => 'required|string',
            ])->validate();

            $data = $request->toArray();
            $project = Project::where([
                'id' => $params['project_id'],
            ])->firstOrFail();

            ksort($data);

            $sign = hash('sha256', urldecode(http_build_query($data)) . $project->api_key);

            if (!hash_equals($sign, $params['signature'])) {
                throw new Exception('Signature not valid!', 401);
            }

            return Project::where('id', $params['project_id'])->first();
        });
    }
}
