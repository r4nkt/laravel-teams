<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Support\Facades\Event;
use R4nkt\Teams\Events\DeletingTeam;
use R4nkt\Teams\Events\TeamDeleted;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * owners can delete owned teams
 *  - events fired
 *  - teams deleted
 *  - members no longer belong
 *  - owners no longer own
 *  - ...?
 */
class DeleteTeamTest extends TestCase
{
    /** @test */
    public function it_deletes_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');

        $player = Player::factory()->create();
        Teams::addTeamMember($team, $player, $owner);

        Teams::deleteTeam($team, $owner);

        $this->assertFalse($team->exists());

        $this->assertSame(0, $owner->allTeams()->count());
        $this->assertSame(0, $owner->ownedTeams()->count());
        $this->assertSame(0, $owner->teams()->count());
        // $this->assertFalse($owner->ownsTeam($team)); // Returns true because it compares Team::owner_id...
        // $this->assertFalse($owner->belongsToTeam($team)); // Returns true because owner ownsTeam()...

        $this->assertSame(0, $player->allTeams()->count());
        $this->assertSame(0, $player->ownedTeams()->count());
        $this->assertSame(0, $player->teams()->count());
        $this->assertFalse($player->ownsTeam($team));
        $this->assertFalse($player->belongsToTeam($team));

        // Events
        Event::assertDispatched(function (DeletingTeam $event) use ($team, $owner) {
            return $event->team === $team
                && $team->owner_id === $owner->getKey();
        });
        Event::assertDispatched(function (TeamDeleted $event) use ($team, $owner) {
            return $event->team === $team
                && $team->owner_id === $owner->getKey();
        });
    }
}
