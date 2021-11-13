<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\TeamInvitation;

interface RevokesTeamInvitations
{
    /**
     * Revoke a pending team invitation.
     */
    public function revoke(BelongsToTeam $revoker, TeamInvitation $invitation): void;
}
