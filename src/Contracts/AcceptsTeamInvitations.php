<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\TeamInvitation;

interface AcceptsTeamInvitations
{
    /**
     * Accept a pending team invitation.
     */
    public function accept(BelongsToTeam $accepter, TeamInvitation $invitation): void;
}
