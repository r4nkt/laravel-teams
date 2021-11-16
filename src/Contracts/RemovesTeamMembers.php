<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface RemovesTeamMembers
{
    /**
     * Remove a member from a team.
     */
    public function remove(Team $team, BelongsToTeam $member, BelongsToTeam $invokedBy): void;
}
