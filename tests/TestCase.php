<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use R4nkt\Teams\Teams;
use R4nkt\Teams\TeamsServiceProvider;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'R4nkt\\Teams\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            TeamsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('teams.models.member', Player::class);

        config()->set('database.default', 'testing');

        include_once __DIR__.'/../database/migrations/create_teams_table.php.stub';
        (new \CreateTeamsTable())->up();

        include_once __DIR__.'/../database/migrations/create_member_team_table.php.stub';
        (new \CreateMemberTeamTable())->up();

        include_once __DIR__.'/../database/migrations/create_team_invitations_table.php.stub';
        (new \CreateInvitationsTable())->up();

        include_once __DIR__.'/database/migrations/create_players_table.php';
        (new \CreatePlayersTable())->up();
    }
}
