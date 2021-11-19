<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface CreatesTeams
{
    /**
     * Validate and create a new team.
     */
    public function create(BelongsToTeam $inkokedBy, string $name, array $attributes = []): Team;
}
