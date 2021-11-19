<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\AcceptsInvitations;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Events\AcceptingInvitation;
use R4nkt\Teams\Events\InvitationAccepted;
use R4nkt\Teams\Models\Invitation;
use R4nkt\Teams\Teams;

class AcceptInvitation implements AcceptsInvitations
{
    /**
     * Accept a pending team invitation.
     */
    public function accept(BelongsToTeam $invokedBy, Invitation $invitation): void
    {
        Gate::forUser($invokedBy)->authorize('acceptInvitation', $invitation);

        AcceptingInvitation::dispatch($invitation, $invokedBy);

        Teams::addTeamMember(
            $invitation->inviter,
            $invitation->team,
            $invitation->invitee,
            $invitation->attributes,
        );

        $invitation->delete();

        InvitationAccepted::dispatch($invitation, $invokedBy);
    }
}
