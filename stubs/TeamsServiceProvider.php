<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use R4nkt\Teams\Actions\AcceptInvitation;
use R4nkt\Teams\Actions\AddTeamMember;
use R4nkt\Teams\Actions\CreateTeam;
use R4nkt\Teams\Actions\DeleteTeam;
use R4nkt\Teams\Actions\InviteTeamMember;
use R4nkt\Teams\Actions\LeaveTeam;
use R4nkt\Teams\Actions\RejectInvitation;
use R4nkt\Teams\Actions\RemoveTeamMember;
use R4nkt\Teams\Actions\RevokeInvitation;
use R4nkt\Teams\Teams;

class TeamsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Teams::acceptInvitationsUsing(AcceptInvitation::class);
        Teams::addTeamMembersUsing(AddTeamMember::class);
        Teams::createTeamsUsing(CreateTeam::class);
        Teams::deleteTeamsUsing(DeleteTeam::class);
        Teams::inviteTeamMembersUsing(InviteTeamMember::class);
        Teams::leaveTeamsUsing(LeaveTeam::class);
        Teams::rejectInvitationsUsing(RejectInvitation::class);
        Teams::removeTeamMembersUsing(RemoveTeamMember::class);
        Teams::revokeInvitationsUsing(RevokeInvitation::class);

        Teams::useMemberModel(config('teams.member_model'));
        Teams::useTeamModel(config('teams.team_model', Teams::teamModel()));
        Teams::useMembershipModel(config('teams.membership_model', Teams::membershipModel()));
        Teams::useInvitationModel(config('teams.teaminvitation_model', Teams::teamInvitationModel()));
    }
}
