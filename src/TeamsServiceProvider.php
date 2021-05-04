<?php

namespace R4nkt\Teams;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use R4nkt\Teams\Commands\TeamsCommand;

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
            ->hasMigration('create_laravel-teams_table')
            ->hasCommand(TeamsCommand::class);
    }
}
