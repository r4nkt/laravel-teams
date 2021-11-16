<?php

namespace R4nkt\Teams;

use R4nkt\Teams\Contracts\AcceptsInvitations;
use R4nkt\Teams\Contracts\AddsTeamMembers;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\CreatesTeams;
use R4nkt\Teams\Contracts\DeletesTeams;
use R4nkt\Teams\Contracts\InvitesTeamMembers;
use R4nkt\Teams\Contracts\LeavesTeams;
use R4nkt\Teams\Contracts\RejectsInvitations;
use R4nkt\Teams\Contracts\RemovesTeamMembers;
use R4nkt\Teams\Contracts\RevokesInvitations;
use R4nkt\Teams\Models\Membership;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\Invitation;

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
    public static string $teamInvitationModel = Invitation::class;

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
    public static function useInvitationModel(string $model): self
    {
        static::$teamInvitationModel = $model;

        return new static;
    }

    /**
     * Register a class / callback that should be used to accept team invitations.
     */
    public static function acceptInvitationsUsing(string $class): void
    {
        app()->singleton(AcceptsInvitations::class, $class);
    }

    public static function acceptInvitation(BelongsToTeam $accepter, Invitation $invitation): void
    {
        app(AcceptsInvitations::class)->accept($accepter, $invitation);
    }

    /**
     * Register a class / callback that should be used to add team members.
     */
    public static function addTeamMembersUsing(string $class): void
    {
        app()->singleton(AddsTeamMembers::class, $class);
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
     */
    public static function inviteTeamMembersUsing(string $class): void
    {
        app()->singleton(InvitesTeamMembers::class, $class);
    }

    public static function inviteTeamMember(BelongsToTeam $inviter, Team $team, BelongsToTeam $invitee, ?array $attributes = null): Invitation
    {
        return app(InvitesTeamMembers::class)->invite($inviter, $team, $invitee, $attributes);
    }

    /**
     * Register a class / callback that should be used when team members leave teams.
     */
    public static function leaveTeamsUsing(string $class): void
    {
        app()->singleton(LeavesTeams::class, $class);
    }

    public static function leaveTeam(BelongsToTeam $member, Team $team): void
    {
        app(LeavesTeams::class)->leave($member, $team);
    }

    /**
     * Register a class / callback that should be used to reject team invitations.
     */
    public static function rejectInvitationsUsing(string $class): void
    {
        app()->singleton(RejectsInvitations::class, $class);
    }

    public static function rejectInvitation(BelongsToTeam $rejecter, Invitation $invitation): void
    {
        app(RejectsInvitations::class)->reject($rejecter, $invitation);
    }

    /**
     * Register a class / callback that should be used to revoke team invitations.
     */
    public static function revokeInvitationsUsing(string $class): void
    {
        app()->singleton(RevokesInvitations::class, $class);
    }

    public static function revokeInvitation(Invitation $invitation, BelongsToTeam $invokedBy): void
    {
        app(RevokesInvitations::class)->revoke($invitation, $invokedBy);
    }

    /**
     * Register a class / callback that should be used to remove team members.
     */
    public static function removeTeamMembersUsing(string $class): void
    {
        app()->singleton(RemovesTeamMembers::class, $class);
    }

    public static function removeTeamMember(Team $team, BelongsToTeam $member, BelongsToTeam $invokedBy): void
    {
        app(RemovesTeamMembers::class)->remove($team, $member, $invokedBy);
    }

    /**
     * Register a class / callback that should be used to update teams.
     */
    // public static function updateTeamsUsing(string $class): void
    // {
    //     app()->singleton(UpdatesTeamNames::class, $class);
    // }
}
