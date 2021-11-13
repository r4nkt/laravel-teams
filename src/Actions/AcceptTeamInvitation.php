<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\AcceptsTeamInvitations;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Events\AcceptingTeamInvitation;
use R4nkt\Teams\Events\TeamInvitationAccepted;
use R4nkt\Teams\Models\TeamInvitation;
use R4nkt\Teams\Teams;

class AcceptTeamInvitation implements AcceptsTeamInvitations
{
    /**
     * Accept a pending team invitation.
     */
    public function accept(BelongsToTeam $accepter, TeamInvitation $invitation): void
    {
        Gate::forUser($accepter)->authorize('acceptTeamInvitation', $invitation);

        AcceptingTeamInvitation::dispatch($invitation, $accepter);

        Teams::addTeamMember($invitation->inviter, $invitation->team, $invitation->invitee, $invitation->attributes);

        $invitation->delete();

        TeamInvitationAccepted::dispatch($invitation, $accepter);
    }
}
