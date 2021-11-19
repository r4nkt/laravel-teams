<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\RejectsInvitations;
use R4nkt\Teams\Events\RejectingInvitation;
use R4nkt\Teams\Events\InvitationRejected;
use R4nkt\Teams\Models\Invitation;

class RejectInvitation implements RejectsInvitations
{
    /**
     * Reject a pending team invitation.
     */
    public function reject(BelongsToTeam $invokedBy, Invitation $invitation): void
    {
        Gate::forUser($invokedBy)->authorize('rejectInvitation', $invitation);

        RejectingInvitation::dispatch($invitation, $invokedBy);

        $invitation->delete();

        InvitationRejected::dispatch($invitation, $invokedBy);
    }
}
