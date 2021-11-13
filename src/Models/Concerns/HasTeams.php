<?php

namespace R4nkt\Teams\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use R4nkt\Teams\Models\Membership;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\TeamInvitation;
use R4nkt\Teams\Teams;

trait HasTeams
{
    /**
     * Get all of the teams the user owns or belongs to.
     */
    public function allTeams(): Collection
    {
        return $this->ownedTeams->merge($this->teams);
    }

    /**
     * Get all of the teams the user owns.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'member_team', 'member_id', 'team_id')
            ->using(Teams::membershipModel())
            ->as('membership')
            ->withPivot('attributes')
            ->withTimestamps();
    }

    /**
     * Determine if the user owns the given team.
     */
    public function ownsTeam(Team $team): bool
    {
        return $this->getKey() == $team->owner_id;
    }

    /**
     * Determine if the user owns the given invitation team.
     */
    public function ownsInvitationTeam(TeamInvitation $invitation)
    {
        return $this->getKey() == $invitation->team->owner_id;
    }

    /**
     * Determine if the user is the inviter for the given invitation.
     */
    public function isInviter(TeamInvitation $invitation)
    {
        return $this->getKey() == $invitation->inviter_id;
    }

    /**
     * Determine if the user is the invitee for the given invitation.
     */
    public function isInvitee(TeamInvitation $invitation)
    {
        return $this->getKey() == $invitation->invitee_id;
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(Team $team): bool
    {
        return $this->ownsTeam($team)
            || $this->teams->contains(function ($t) use ($team) {
                return $t->id === $team->id;
            });
    }

    /**
     * Leave the team
     */
    public function leaveTeam(Team $team): void
    {
        /** @todo This is basically implemented via Team::removeMember().  Any reason to duplicate it here? */
    }

    /**
     * Get all of the pending invitations for the team that the member has received.
     */
    public function receivedInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'invitee_id');
    }

    /**
     * Get all of the pending invitations for the team that the member has sent.
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'inviter_id');
    }
}
