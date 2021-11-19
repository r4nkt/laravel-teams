<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Invitation;

interface RejectsInvitations
{
    /**
     * Reject a pending team invitation.
     */
    public function reject(BelongsToTeam $invokedBy, Invitation $invitation): void;
}
