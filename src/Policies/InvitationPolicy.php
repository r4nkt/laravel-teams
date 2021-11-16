<?php

namespace R4nkt\Teams\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\Invitation;

class InvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any team invitations.
     *
     * @return mixed
     */
    public function viewAny(BelongsToTeam $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the team invitation.
     *
     * @return mixed
     */
    public function view(BelongsToTeam $user, Invitation $invitation)
    {
        return $user->isInvitee($invitation)
            || $user->ownsInvitationTeam($invitation);
    }

    /**
     * Determine whether the user can create team invitations.
     *
     * @return mixed
     */
    public function create(BelongsToTeam $user, Team $team, BelongsToTeam $invitee, ?array $attributes = null)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can update the team invitation.
     *
     * @return mixed
     */
    public function update(BelongsToTeam $user, Invitation $invitation)
    {
        return $user->isInviter($invitation)
            || $user->ownsInvitationTeam($invitation);
    }

    /**
     * Determine whether the user can accept a team invitation.
     *
     * @return mixed
     */
    public function acceptInvitation(BelongsToTeam $user, Invitation $invitation)
    {
        return $user->isInvitee($invitation);
    }

    /**
     * Determine whether the user can reject a team invitation.
     *
     * @return mixed
     */
    public function rejectInvitation(BelongsToTeam $user, Invitation $invitation)
    {
        return $user->isInvitee($invitation);
    }

    /**
     * Determine whether the user can revoke a team invitation.
     *
     * @return mixed
     */
    public function revokeInvitation(BelongsToTeam $user, Invitation $invitation)
    {
        return $user->isInvitee($invitation)
            || $user->ownsInvitationTeam($invitation);
    }
}
