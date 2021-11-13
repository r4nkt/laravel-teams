<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface AddsTeamMembers
{
    /**
     * Add a member to a team.
     */
    public function add(BelongsToTeam $owner, Team $team, BelongsToTeam $member, array $attributes = []): void;
}
