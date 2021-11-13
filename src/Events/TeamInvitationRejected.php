<?php

namespace R4nkt\Teams\Events;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\TeamInvitation;

class TeamInvitationRejected extends TeamInvitationEvent
{
    public function __construct(
        TeamInvitation $invitation,
        public BelongsToTeam $rejecter,
    )
    {
        parent::__construct($invitation);
    }
}
