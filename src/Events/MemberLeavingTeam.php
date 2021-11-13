<?php

namespace R4nkt\Teams\Events;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

class MemberLeavingTeam extends TeamEvent
{
    public function __construct(
        Team $team,
        public BelongsToTeam $member,
    )
    {
        parent::__construct($team);
    }
}
