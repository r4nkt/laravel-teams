<?php

namespace R4nkt\Teams\Contracts;

use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

interface DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(BelongsToTeam $invokedBy, Team $team): void;
}
