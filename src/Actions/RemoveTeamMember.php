<?php

namespace R4nkt\Teams\Actions;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\RemovesTeamMembers;
use R4nkt\Teams\Events\RemovingTeamMember;
use R4nkt\Teams\Events\TeamMemberRemoved;
use R4nkt\Teams\Models\Team;

class RemoveTeamMember implements RemovesTeamMembers
{
    /**
     * Remove a member from a team.
     */
    public function remove(BelongsToTeam $owner, Team $team, BelongsToTeam $member): void
    {
        Gate::forUser($owner)->authorize('removeTeamMember', $team);

        // $this->validate($team, $member);

        RemovingTeamMember::dispatch($team, $member, $owner);

        $team->members()->detach($member);

        TeamMemberRemoved::dispatch($team, $member, $owner);

        $team->refresh('members');
    }

    /**
     * Validate the add member operation.
     */
    // protected function validate(Team $team, BelongsToTeam $member, array $attributes): void
    // {
    //     Validator::make([
    //         'member_id' => $member->getKey(),
    //         'attributes' => $attributes,
    //         'role' => $attributes['role'] ?? null,
    //     ], $this->rules($team, $attributes), [
    //         'member_id.exists' => __('We were unable to find this member.'),
    //     ])->after(
    //         $this->ensureMemberIsNotAlreadyOnTeam($team, $member)
    //     )->validateWithBag('addTeamMember');
    // }

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    // protected function rules(Team $team, array $attributes)
    // {
    //     return array_filter([
    //         'member_id' => ['required', 'exists:players,id'],
    //         'attributes' => ['nullable', 'array'],
    //         'role' => ($attributes && array_key_exists('role', $attributes)) ? ['string'] : null,
    //     ]);
    // }

    // *
    //  * Ensure that the member is not already on the team.

    // protected function ensureMemberIsNotAlreadyOnTeam(Team $team, BelongsToTeam $member): Closure
    // {
    //     return function ($validator) use ($member, $team) {
    //         $validator->errors()->addIf(
    //             $team->hasMember($member),
    //             'member_id',
    //             __('This member already belongs to the team.')
    //         );
    //     };
    // }
}
