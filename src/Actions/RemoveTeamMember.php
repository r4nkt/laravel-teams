<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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
    public function remove(BelongsToTeam $invokedBy, Team $team, BelongsToTeam $member): void
    {
        Gate::forUser($invokedBy)->authorize('removeTeamMember', $team);

        $this->validate($team, $member, $invokedBy);

        RemovingTeamMember::dispatch($team, $member, $invokedBy);

        $team->members()->detach($member);

        TeamMemberRemoved::dispatch($team, $member, $invokedBy);

        $team->refresh('members');
    }

    /**
     * Validate the remove member operation.
     */
    protected function validate(Team $team, BelongsToTeam $member, BelongsToTeam $invokedBy): void
    {
        if ($member->getKey() === $invokedBy->getKey()) {
            throw ValidationException::withMessages([
                'member' => __('One cannot remove oneself from a team.'),
            ])->errorBag('removeTeamMember');
        }

        if (! $team->hasMember($member)) {
            throw ValidationException::withMessages([
                'member' => __('This member does not belong to the team.'),
            ])->errorBag('removeTeamMember');
        }
    }
}
