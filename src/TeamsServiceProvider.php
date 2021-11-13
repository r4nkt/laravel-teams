<?php

namespace R4nkt\Teams;

use R4nkt\Teams\Commands\TeamsCommand;
use R4nkt\Teams\Providers\EventServiceProvider;
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

        $this->app->register(EventServiceProvider::class);
    }

    public function packageBooted()
    {
        $this->configurePublishing();
    }

    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                // __DIR__.'/../stubs/CreateNewUser.php' => app_path('Actions/Fortify/CreateNewUser.php'),
                __DIR__.'/../stubs/TeamsServiceProvider.php' => app_path('Providers/TeamsServiceProvider.php'),
                // __DIR__.'/../stubs/PasswordValidationRules.php' => app_path('Actions/Fortify/PasswordValidationRules.php'),
                // __DIR__.'/../stubs/ResetUserPassword.php' => app_path('Actions/Fortify/ResetUserPassword.php'),
                // __DIR__.'/../stubs/UpdateUserProfileInformation.php' => app_path('Actions/Fortify/UpdateUserProfileInformation.php'),
                // __DIR__.'/../stubs/UpdateUserPassword.php' => app_path('Actions/Fortify/UpdateUserPassword.php'),
            ], 'r4nkt-teams-support');
        }
    }
}
