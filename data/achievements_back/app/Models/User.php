<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property bool $active
 * @property string $name
 * @property string $email
 * @property string $password
 * @property-read BelongsToMany $groups
 * @property-read HasMany $projectsUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'bool',
    ];

    public function projectsAllowedForAdministrationIds(): array
    {
        $ids = [];
        foreach ($this->projectsUser as $projectUser) {
            if (array_filter($projectUser->groups->toArray(), fn($group) => in_array($group['name'], [
                'ProjectAdmin',
                'ProjectManager',
            ]))) {
                $ids[] = $projectUser->project_id;
            }
        }

        return $ids;
    }

    public function isSuperUser(): bool
    {
        return !!array_filter($this->groups->toArray(), fn($group) => $group['name'] === 'SuperUser');
    }

    public function isProjectAdmin(): bool
    {
        foreach ($this->projectsUser as $projectUser) {
            if (array_filter($projectUser->groups->toArray(), fn($group) => $group['name'] == 'ProjectAdmin')) {
                return true;
            }
        }

        return false;
    }

    public function isProjectManager(): bool
    {
        foreach ($this->projectsUser as $projectUser) {
            if (array_filter($projectUser->groups->toArray(), fn($group) => $group['name'] == 'ProjectManager')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
        );
    }

    public function nameGroups(): array
    {
        return $this->groups()->map(function ($group) {
            return array_merge($user->toArray(), [
                'groups' => $user->getGroupNames(),
            ]);
        });
    }

    /**
     * @return HasMany
     */
    public function projectsUser(): HasMany
    {
        return $this->hasMany(
            ProjectUser::class,
        );
    }

    /**
     * @return array
     */
    public function getGroupNames(): array
    {
        return array_column($this->groups->toArray(), 'name');
    }
}
