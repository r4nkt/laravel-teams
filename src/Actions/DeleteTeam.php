<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\DeletesTeams;
use R4nkt\Teams\Models\Team;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team, BelongsToTeam $invokedBy): void
    {
        Gate::forUser($invokedBy)->authorize('delete', $team);

        // DeletingTeam dispatched automatically via model...

        $team->purge();

        // TeamDeleted dispatched automatically via model...
    }
}
