<?php

namespace R4nkt\Teams;

use R4nkt\Teams\Contracts\AcceptsTeamInvitations;
use R4nkt\Teams\Contracts\AddsTeamMembers;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\CreatesTeams;
use R4nkt\Teams\Contracts\DeletesTeams;
use R4nkt\Teams\Contracts\InvitesTeamMembers;
use R4nkt\Teams\Contracts\LeavesTeams;
use R4nkt\Teams\Contracts\RejectsTeamInvitations;
use R4nkt\Teams\Contracts\RevokesTeamInvitations;
use R4nkt\Teams\Models\Membership;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\TeamInvitation;

class Teams
{
    /**
     * The member model that should be used by Teams.
     */
    public static string $memberModel;

    /**
     * The team model that should be used by Teams.
     */
    public static string $teamModel = Team::class;

    /**
     * The membership model that should be used by Teams.
     */
    public static string $membershipModel = Membership::class;

    /**
     * The team invitation model that should be used by Teams.
     */
    public static string $teamInvitationModel = TeamInvitation::class;

    /**
     * Get the name of the member model used by the application.
     */
    public static function memberModel(): string
    {
        return static::$memberModel;
    }

    /**
     * Get a new instance of the member model.
     */
    public static function newMemberModel(): object
    {
        $model = static::memberModel();

        return new $model;
    }

    /**
     * Specify the member model that should be used by Teams.
     */
    public static function useMemberModel(string $model): self
    {
        static::$memberModel = $model;

        return new static;
    }

    /**
     * Get the name of the team model used by the application.
     */
    public static function teamModel(): string
    {
        return static::$teamModel;
    }

    /**
     * Get a new instance of the team model.
     */
    public static function newTeamModel(): object
    {
        $model = static::teamModel();

        return new $model;
    }

    /**
     * Specify the team model that should be used by Teams.
     */
    public static function useTeamModel(string $model): self
    {
        static::$teamModel = $model;

        return new static;
    }

    /**
     * Get the name of the membership model used by the application.
     */
    public static function membershipModel(): string
    {
        return static::$membershipModel;
    }

    /**
     * Specify the membership model that should be used by Teams.
     */
    public static function useMembershipModel(string $model): self
    {
        static::$membershipModel = $model;

        return new static;
    }

    /**
     * Get the name of the team invitation model used by the application.
     */
    public static function teamInvitationModel(): string
    {
        return static::$teamInvitationModel;
    }

    /**
     * Specify the team invitation model that should be used by Teams.
     */
    public static function useTeamInvitationModel(string $model): self
    {
        static::$teamInvitationModel = $model;

        return new static;
    }

    /**
     * Register a class / callback that should be used to accept team invitations.
     *
     * @param  string  $class
     * @return void
     */
    public static function acceptTeamInvitationsUsing(string $class)
    {
        return app()->singleton(AcceptsTeamInvitations::class, $class);
    }

    public static function acceptTeamInvitation(BelongsToTeam $accepter, TeamInvitation $invitation): void
    {
        app(AcceptsTeamInvitations::class)->accept($accepter, $invitation);
    }

    /**
     * Register a class / callback that should be used to add team members.
     *
     * @param  string  $class
     * @return void
     */
    public static function addTeamMembersUsing(string $class)
    {
        return app()->singleton(AddsTeamMembers::class, $class);
    }

    public static function addTeamMember(BelongsToTeam $owner, Team $team, BelongsToTeam $member, array $attributes = []): void
    {
        app(AddsTeamMembers::class)->add($owner, $team, $member, $attributes);
    }

    /**
     * Register a class / callback that should be used to create teams.
     */
    public static function createTeamsUsing(string $class): void
    {
        app()->singleton(CreatesTeams::class, $class);
    }

    public static function createTeam(BelongsToTeam $owner, string $name, array $input = []): Team
    {
        return app(CreatesTeams::class)->create($owner, $name, $input);
    }

    /**
     * Register a class / callback that should be used to delete teams.
     */
    public static function deleteTeamsUsing(string $class): void
    {
        app()->singleton(DeletesTeams::class, $class);
    }

    public static function deleteTeam(BelongsToTeam $owner, Team $team)
    {
        app(DeletesTeams::class)->delete($owner, $team);
    }

    /**
     * Register a class / callback that should be used to invite team members.
     *
     * @param  string  $class
     * @return void
     */
    public static function inviteTeamMembersUsing(string $class)
    {
        return app()->singleton(InvitesTeamMembers::class, $class);
    }

    public static function inviteTeamMember(BelongsToTeam $inviter, Team $team, BelongsToTeam $invitee, ?array $attributes = null): TeamInvitation
    {
        return app(InvitesTeamMembers::class)->invite($inviter, $team, $invitee, $attributes);
    }

    /**
     * Register a class / callback that should be used when team members leave teams.
     *
     * @param  string  $class
     * @return void
     */
    public static function leaveTeamsUsing(string $class)
    {
        return app()->singleton(LeavesTeams::class, $class);
    }

    public static function leaveTeam(BelongsToTeam $member, Team $team): void
    {
        app(LeavesTeams::class)->leave($member, $team);
    }

    /**
     * Register a class / callback that should be used to reject team invitations.
     *
     * @param  string  $class
     * @return void
     */
    public static function rejectTeamInvitationsUsing(string $class)
    {
        return app()->singleton(RejectsTeamInvitations::class, $class);
    }

    public static function rejectTeamInvitation(BelongsToTeam $rejecter, TeamInvitation $invitation): void
    {
        app(RejectsTeamInvitations::class)->reject($rejecter, $invitation);
    }

    /**
     * Register a class / callback that should be used to revoke team invitations.
     *
     * @param  string  $class
     * @return void
     */
    public static function revokeTeamInvitationsUsing(string $class)
    {
        return app()->singleton(RevokesTeamInvitations::class, $class);
    }

    public static function revokeTeamInvitation(BelongsToTeam $revoker, TeamInvitation $invitation): void
    {
        app(RevokesTeamInvitations::class)->revoke($revoker, $invitation);
    }

    /**
     * Register a class / callback that should be used to remove team members.
     *
     * @param  string  $class
     * @return void
     */
    // public static function removeTeamMembersUsing(string $class)
    // {
    //     return app()->singleton(RemovesTeamMembers::class, $class);
    // }

    /**
     * Register a class / callback that should be used to update teams.
     *
     * @param  string  $class
     * @return void
     */
    // public static function updateTeamsUsing(string $class)
    // {
    //     return app()->singleton(UpdatesTeamNames::class, $class);
    // }
}
