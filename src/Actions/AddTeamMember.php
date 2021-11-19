<?php

namespace R4nkt\Teams\Actions;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use R4nkt\Teams\Contracts\AddsTeamMembers;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Events\AddingTeamMember;
use R4nkt\Teams\Events\TeamMemberAdded;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;

class AddTeamMember implements AddsTeamMembers
{
    /**
     * Add a member to a team.
     */
    public function add(Team $team, BelongsToTeam $member, BelongsToTeam $invokedBy, array $attributes = []): void
    {
        Gate::forUser($invokedBy)->authorize('addTeamMember', $team);

        $this->validate($team, $member, $attributes);

        AddingTeamMember::dispatch($team, $member, $invokedBy, $attributes);

        $team->members()->attach($member, ['attributes' => $attributes]);

        TeamMemberAdded::dispatch($team, $member, $invokedBy, $attributes);

        $team->refresh('members');
    }

    /**
     * Validate the add member operation.
     */
    protected function validate(Team $team, BelongsToTeam $member, array $attributes): void
    {
        Validator::make([
            'member' => $member->getKey(),
            'attributes' => $attributes,
            'role' => $attributes['role'] ?? null,
        ], $this->rules($team, $attributes), [
            'member.exists' => __('We were unable to find this member.'),
        ])->after(
            $this->ensureMemberIsNotAlreadyOnTeam($team, $member)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    protected function rules(Team $team, array $attributes)
    {
        $memberModel = Teams::newMemberModel();

        return array_filter([
            'member' => ['required', "exists:{$memberModel->getTable()},{$memberModel->getKeyName()}"],
            'attributes' => ['nullable', 'array'],
            'role' => ($attributes && array_key_exists('role', $attributes)) ? ['string'] : null,
        ]);
    }

    /**
     * Ensure that the member is not already on the team.
     */
    protected function ensureMemberIsNotAlreadyOnTeam(Team $team, BelongsToTeam $member): Closure
    {
        return function ($validator) use ($member, $team) {
            $validator->errors()->addIf(
                $team->hasMember($member),
                'member',
                __('This member already belongs to the team.'),
            );
        };
    }
}
