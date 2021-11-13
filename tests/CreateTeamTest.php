<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use R4nkt\Teams\Events\CreatingTeam;
use R4nkt\Teams\Events\TeamCreated;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * "players" can create teams
 *  - events fired
 *  - they are the designated owner
 *  - ...?
 *
 * @todo Test that description, custom data can be passed during creation.
 */
class CreateTeamTest extends TestCase
{
    /** @test */
    public function it_can_create_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();

        $team = Teams::createTeam($owner, 'team name');

        $this->assertNotNull($team);
        $this->assertSame($team->owner->id, $owner->id);

        // Team functionality
        $this->assertTrue($team->allMembers()->contains($owner));
        $this->assertFalse($team->members->contains($owner));
        $this->assertTrue($team->hasMember($owner));
        $this->assertSame($team->owner->getKey(), $owner->getKey());

        // Member functionality
        $this->assertTrue($owner->allTeams()->contains($team));
        $this->assertTrue($owner->ownedTeams->contains($team));
        $this->assertFalse($owner->teams->contains($team));
        $this->assertTrue($owner->ownsTeam($team));
        $this->assertTrue($owner->belongsToTeam($team));

        // Events
        Event::assertDispatched(function (CreatingTeam $event) use ($owner) {
            return $event->team->owner_id === $owner->id;
        });
        Event::assertDispatched(function (TeamCreated $event) use ($team, $owner) {
            return $event->team->id === $team->id
                && $event->team->owner_id === $owner->id;
        });
    }

    /**
     * @test
     * @dataProvider provideInvalidTeamNames
     */
    public function it_requires_a_valid_name($invalidName)
    {
        $owner = Player::factory()->create();

        $this->expectException(ValidationException::class);

        Teams::createTeam($owner, $invalidName);
    }

    public function provideInvalidTeamNames()
    {
        return [
            [
                '', // name: empty string
            ],
            [
                str_repeat('t', 256), // name: exceeds max length
            ],
        ];
    }

    /** @test */
    public function it_does_not_allow_a_member_to_create_teams_with_the_same_name()
    {
        $owner = Player::factory()->create();

        $teamName = 'new team';

        $team = Teams::createTeam($owner, $teamName);

        $this->expectException(ValidationException::class);

        try {
            Teams::createTeam($owner, $teamName);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors());
            $this->assertSame(1, Team::count());

            throw $e;
        }
    }

    /** @test */
    public function it_allows_two_members_to_create_teams_with_the_same_name()
    {
        $ownerA = Player::factory()->create();
        $ownerB = Player::factory()->create();

        $teamName = 'new team';

        $teamA = Teams::createTeam($ownerA, $teamName);
        $teamB = Teams::createTeam($ownerB, $teamName);

        $this->assertSame(2, Team::count());
        $this->assertSame(1, $ownerA->ownedTeams()->count());
        $this->assertSame(1, $ownerB->ownedTeams()->count());
    }
}
