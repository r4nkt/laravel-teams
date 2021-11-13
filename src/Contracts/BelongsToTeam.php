<?php

namespace R4nkt\Teams\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\TeamInvitation;

interface BelongsToTeam
{
    /**
     * Get the value of the model's primary key.
     *
     * NOTE: Extending from Laravel's Illuminate\Database\Eloquent\Model class
     * provides this method.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get all of the teams the user owns or belongs to.
     */
    public function allTeams(): Collection;

    /**
     * Get all of the teams the user owns.
     */
    public function ownedTeams(): HasMany;

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams(): BelongsToMany;

    /**
     * Determine if the user owns the given team.
     */
    public function ownsTeam(Team $team): bool;

    /**
     * Determine if the user owns the given invitation team.
     */
    public function ownsInvitationTeam(TeamInvitation $invitation);

    /**
     * Determine if the user is the inviter for the given invitation.
     */
    public function isInviter(TeamInvitation $invitation);

    /**
     * Determine if the user is the invitee for the given invitation.
     */
    public function isInvitee(TeamInvitation $invitation);

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(Team $team): bool;

    /**
     * Leave the team
     *
     * @todo This is basically implemented via Team::removeMember().  Any reason to duplicate it here?
     */
    public function leaveTeam(Team $team): void;

    /**
     * Get all of the pending invitations for the team that the member has received.
     */
    public function receivedInvitations(): HasMany;

    /**
     * Get all of the pending invitations for the team that the member has sent.
     */
    public function sentInvitations(): HasMany;
}
