<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\CreatesTeams;
use R4nkt\Teams\Models\Team;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team.
     */
    public function create(BelongsToTeam $invokedBy, string $name, array $attributes = []): Team
    {
        Gate::forUser($invokedBy)->authorize('create', Team::class);

        $attributes['name'] = $name;

        Validator::make($attributes, $this->rules($invokedBy))
            ->validateWithBag('createTeam');

        // CreatingTeam dispatched automatically via model...

        $team = Team::create([
            'owner_id' => $invokedBy->getKey(),
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? null,
            'custom_data' => $attributes['custom_data'] ?? null,
        ]);

        // TeamCreated dispatched automatically via model...

        return $team;
    }

    /**
     * Get the validation rules for creating a team.
     */
    protected function rules(BelongsToTeam $invokedBy): array
    {
        return array_filter([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams')->where(function ($query) use ($invokedBy) {
                    $query->where('owner_id', $invokedBy->getKey());
                }),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'custom_data' => [
                'nullable',
                'nullable',
                'string',
                'max:255',
            ],
        ]);
    }
}
