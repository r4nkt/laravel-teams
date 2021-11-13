<?php

namespace R4nkt\Teams\Events;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\TeamInvitation;

class RevokingTeamInvitation extends TeamInvitationEvent
{
    public function __construct(
        TeamInvitation $invitation,
        public BelongsToTeam $revoker,
    )
    {
        parent::__construct($invitation);
    }
}
