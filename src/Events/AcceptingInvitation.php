<?php

namespace R4nkt\Teams\Events;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Invitation;

class AcceptingInvitation extends InvitationEvent
{
    public function __construct(
        Invitation $invitation,
        public BelongsToTeam $invokedBy,
    )
    {
        parent::__construct($invitation);
    }
}
