<?php

namespace R4nkt\Teams\Actions;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\RejectsTeamInvitations;
use R4nkt\Teams\Events\RejectingTeamInvitation;
use R4nkt\Teams\Events\TeamInvitationRejected;
use R4nkt\Teams\Models\TeamInvitation;

class RejectTeamInvitation implements RejectsTeamInvitations
{
    /**
     * Reject a pending team invitation.
     */
    public function reject(BelongsToTeam $rejecter, TeamInvitation $invitation): void
    {
        Gate::forUser($rejecter)->authorize('rejectTeamInvitation', $invitation);

        RejectingTeamInvitation::dispatch($invitation, $rejecter);

        $invitation->delete();

        TeamInvitationRejected::dispatch($invitation, $rejecter);
    }
}
