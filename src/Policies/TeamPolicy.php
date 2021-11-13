<?php

namespace R4nkt\Teams\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any teams.
     *
     * @return mixed
     */
    public function viewAny(BelongsToTeam $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the team.
     *
     * @return mixed
     */
    public function view(BelongsToTeam $user, Team $team)
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create teams.
     *
     * @return mixed
     */
    public function create(BelongsToTeam $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the team.
     *
     * @return mixed
     */
    public function update(BelongsToTeam $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can delete the team.
     *
     * @return mixed
     */
    public function delete(BelongsToTeam $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can add team members.
     *
     * @return mixed
     */
    public function addTeamMember(BelongsToTeam $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can update team member attributes, e.g., role, permissions, etc.
     *
     * @return mixed
     */
    public function updateTeamMember(BelongsToTeam $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can remove team members.
     *
     * @return mixed
     */
    public function removeTeamMember(BelongsToTeam $user, Team $team)
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can leave a team.
     *
     * @return mixed
     */
    public function leaveTeam(BelongsToTeam $user, Team $team)
    {
        return ! $user->ownsTeam($team)
            && $user->belongsToTeam($team);
    }
}
