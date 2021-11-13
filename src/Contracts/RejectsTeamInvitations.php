<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\TeamInvitation;

interface RejectsTeamInvitations
{
    /**
     * Reject a pending team invitation.
     */
    public function reject(BelongsToTeam $rejecter, TeamInvitation $invitation): void;
}
