<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\Invitation;

interface InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     */
    public function invite(Team $team, BelongsToTeam $member, BelongsToTeam $invokedBy, ?array $attributes = null): Invitation;
}
