<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uid',
        'name',
        'email',
        'phone',
        'dob',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date:U'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role' => 'user',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Generates and returns a JWT for current user
     *
     * @return string
     */
    public function getToken(): string
    {
        return auth('api')->tokenById($this->id);
    }

    /**
     * Compares user's role and check if its `admin` or not
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isAdmin(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['role'] === 'admin',
        );
    }

    /**
     * Parses date of birth of the user (if present) and converts it into eye-
     * friendly format
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function dateOfBirth(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!$attributes['dob']) return null;

                $dob = new Carbon($attributes['dob']);
                return $dob->format('jS F Y');
            }
        );
    }

    /**
     * Parses date of birth of the user (if present) and converts it into
     * UNIX timestamp
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function unixDateOfBirth(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): int|null {
                if (!$attributes['dob']) return null;

                $dob = new Carbon($attributes['dob']);
                return (int) $dob->format('U');
            }
        );
    }

    /**
     * Parses date of birth of the user (if present) and converts it into
     * short string
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function shortDateOfBirth(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string|null {
                if (!$attributes['dob']) return null;

                $dob = new Carbon($attributes['dob']);
                return (string) $dob->format('Y-m-d');
            }
        );
    }

    /**
     * Calculates workload of the user and also caches it to provide maximum
     * efficiency
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function workload(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                return Cache::remember(
                    sprintf('user.%s.workload', $attributes['id']),
                    60, // One minute
                    function (): int {

                        $projects = $this
                            ->projects()
                            ->select('workload') // Only select workload column
                            ->distinct() // Remove duplicates
                            ->get()
                            ->unique(); // Remove duplicates

                        if ($projects->count() == 0) return 0;

                        return ($projects
                            ->map(fn (Project $project) => (int) $project->workload)
                            // Add all workloads
                            ->reduce(fn ($carry, $item) => $carry + $item, 0)
                            / ($projects->count() ?: 1) // Division by zero
                        );
                    }
                );
            }
        );
    }

    /**
     * If a user has multiple roles in a single project then multiple records
     * of the same projects are returned having different `pivot.role` value,
     * this method combines all duplicate projects into a single record and
     * creates a `roles` array property on the model having all roles assigned
     * to the user in that particular project
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function projectsWithRoles(): Attribute
    {
        return Attribute::make(
            get: function (): Collection {
                return $this
                    ->projects
                    ->groupBy('id')
                    ->map(function (Collection $group) {
                        $__project = $group->first();

                        // All roles will be stored in an array
                        $__project->roles = new Collection();

                        $group->each(function (Project $project) use ($__project) {
                            // Save all the roles in a single array
                            return $__project
                                ->roles
                                ->push($project->pivot->role);
                        });

                        if ($__project->leader_id === $this->id)
                            $__project->roles->push('leader');

                        // Remove duplicate roles
                        $__project->roles = $__project->roles->unique();

                        return $__project;
                    })
                    ->values();
            }
        );
    }

    /**
     * If a user has multiple roles in a single project then multiple records
     * of the same projects are returned having different `pivot.role` value,
     * this method combines all duplicate projects into a single record.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function uniqueProjects(): Attribute
    {
        return Attribute::make(
            get: function (): Collection {
                return $this
                    ->projects
                    ->groupBy('id')
                    ->map(fn (Collection $group) => $group->first());
            }
        );
    }

    /**
     * A user can belong to many projects with multiple roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this
            ->belongsToMany(Project::class, 'project_members')
            ->withPivot('role');
    }

    /**
     * Gets the progress of all projects the user is involved in.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function projectProgress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $projects = $this->uniqueProjects;

                // Get sum or all project's progress
                $progress = (int) $projects
                    ->reduce(function (int $carry, Project $project) {
                        return $carry + $project->progress;
                    }, 0);

                // Each project has a 100% max progress
                $total = $projects->count() * 100;

                // Simple math formula for taking percentage
                return ($progress * 100) / $total;
            }
        );
    }

    /**
     * A user can be leader of many projects
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leaders(): HasMany
    {
        return $this->hasMany(Project::class, 'leader_id');
    }

    /**
     * Projects in which user has the `BA` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bas(): BelongsToMany
    {
        return $this
            ->projects()
            ->wherePivot('role', 'ba');
    }

    /**
     * Projects in which user has the `LD` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lds(): BelongsToMany
    {
        return $this
            ->projects()
            ->wherePivot('role', 'ld');
    }

    /**
     * Projects in which user has the `DA` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function das(): BelongsToMany
    {
        return $this
            ->projects()
            ->wherePivot('role', 'da');
    }
}
