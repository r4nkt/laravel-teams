<?php

namespace R4nkt\Teams;

use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Commands\TeamsCommand;
use R4nkt\Teams\Teams;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TeamsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-teams')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations(
                'create_teams_table',
                'create_member_team_table',
                'create_team_invitations_table',
            )
            ->hasCommand(TeamsCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->bind('laravel-teams', function () {
            return new Teams();
        });
    }

    public function packageBooted()
    {
        $this->configurePublishing();

        $this->configureTeams();
    }

    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                // __DIR__.'/../stubs/CreateNewUser.php' => app_path('Actions/Fortify/CreateNewUser.php'),
                // __DIR__.'/../stubs/TeamsServiceProvider.php' => app_path('Providers/TeamsServiceProvider.php'),
                // __DIR__.'/../stubs/PasswordValidationRules.php' => app_path('Actions/Fortify/PasswordValidationRules.php'),
                // __DIR__.'/../stubs/ResetUserPassword.php' => app_path('Actions/Fortify/ResetUserPassword.php'),
                // __DIR__.'/../stubs/UpdateUserProfileInformation.php' => app_path('Actions/Fortify/UpdateUserProfileInformation.php'),
                // __DIR__.'/../stubs/UpdateUserPassword.php' => app_path('Actions/Fortify/UpdateUserPassword.php'),
            ], 'r4nkt-teams-support');
        }
    }

    public function configureTeams()
    {
        /**
         * NOTE: Order is important...!
         */
        $this->configureModels();
        $this->configureActions();
        $this->configurePolicies();
    }

    protected function configureModels()
    {
        Teams::useMemberModel($this->app->config['teams.models.member']);
        Teams::useTeamModel($this->app->config['teams.models.team']);
        Teams::useMembershipModel($this->app->config['teams.models.membership']);
        Teams::useInvitationModel($this->app->config['teams.models.invitation']);
    }

    protected function configureActions()
    {
        Teams::createTeamsUsing($this->app->config['teams.actions.create_teams']);
        // Teams::updateTeamsUsing($this->app->config['teams.actions.update_teams']);
        // Teams::transferTeamsUsing($this->app->config['teams.actions.transfer_teams']);
        Teams::deleteTeamsUsing($this->app->config['teams.actions.delete_teams']);

        Teams::addTeamMembersUsing($this->app->config['teams.actions.add_team_members']);
        // Teams::updateTeamMembersUsing($this->app->config['teams.actions.update_team_members']);
        Teams::removeTeamMembersUsing($this->app->config['teams.actions.remove_team_members']);

        Teams::inviteTeamMembersUsing($this->app->config['teams.actions.invite_team_members']);
        Teams::acceptInvitationsUsing($this->app->config['teams.actions.accept_invitations']);
        Teams::rejectInvitationsUsing($this->app->config['teams.actions.reject_invitations']);
        Teams::revokeInvitationsUsing($this->app->config['teams.actions.revoke_invitations']);

        Teams::leaveTeamsUsing($this->app->config['teams.actions.leave_teams']);
    }

    protected function configurePolicies()
    {
        Gate::policy(Teams::teamModel(), $this->app->config['teams.policies.team']);
        Gate::policy(Teams::invitationModel(), $this->app->config['teams.policies.invitation']);
    }
}
