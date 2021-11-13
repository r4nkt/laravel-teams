<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface LeavesTeams
{
    /**
     * Leave a team.
     */
    public function leave(BelongsToTeam $member, Team $team): void;
}
