<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\RevokesTeamInvitations;
use R4nkt\Teams\Events\RevokingTeamInvitation;
use R4nkt\Teams\Events\TeamInvitationRevoked;
use R4nkt\Teams\Models\TeamInvitation;

class RevokeTeamInvitation implements RevokesTeamInvitations
{
    /**
     * Revoke a pending team invitation.
     */
    public function revoke(BelongsToTeam $revoker, TeamInvitation $invitation): void
    {
        Gate::forUser($revoker)->authorize('revokeTeamInvitation', $invitation);

        RevokingTeamInvitation::dispatch($invitation, $revoker);

        $invitation->delete();

        TeamInvitationRevoked::dispatch($invitation, $revoker);
    }
}
