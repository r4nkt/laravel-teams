<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\RevokesInvitations;
use R4nkt\Teams\Events\RevokingInvitation;
use R4nkt\Teams\Events\InvitationRevoked;
use R4nkt\Teams\Models\Invitation;

class RevokeInvitation implements RevokesInvitations
{
    /**
     * Revoke a pending team invitation.
     */
    public function revoke(BelongsToTeam $invokedBy, Invitation $invitation): void
    {
        Gate::forUser($invokedBy)->authorize('revokeInvitation', $invitation);

        RevokingInvitation::dispatch($invitation, $invokedBy);

        $invitation->delete();

        InvitationRevoked::dispatch($invitation, $invokedBy);
    }
}
