<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\TeamInvitation;

interface InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     */
    public function invite(BelongsToTeam $inviter, Team $team, BelongsToTeam $invitee, ?array $attributes = null): TeamInvitation;
}