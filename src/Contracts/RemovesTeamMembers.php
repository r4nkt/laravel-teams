<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface RemovesTeamMembers
{
    /**
     * Remove a member from a team.
     */
    public function remove(BelongsToTeam $invokedBy, Team $team, BelongsToTeam $member): void;
}
