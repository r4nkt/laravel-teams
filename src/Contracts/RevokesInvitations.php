<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Invitation;

interface RevokesInvitations
{
    /**
     * Revoke a pending team invitation.
     */
    public function revoke(BelongsToTeam $invokedBy, Invitation $invitation): void;
}
