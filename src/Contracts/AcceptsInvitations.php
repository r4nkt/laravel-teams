<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Invitation;

interface AcceptsInvitations
{
    /**
     * Accept a pending team invitation.
     */
    public function accept(BelongsToTeam $accepter, Invitation $invitation): void;
}
