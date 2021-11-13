<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\LeavesTeams;
use R4nkt\Teams\Events\MemberLeavingTeam;
use R4nkt\Teams\Events\MemberLeftTeam;
use R4nkt\Teams\Models\Team;

class LeaveTeam implements LeavesTeams
{
    /**
     * Leave a team.
     */
    public function leave(BelongsToTeam $member, Team $team): void
    {
        Gate::forUser($member)->authorize('leaveTeam', $team);

        MemberLeavingTeam::dispatch($team, $member);

        $team->removeMember($member);

        MemberLeftTeam::dispatch($team, $member);
    }
}
