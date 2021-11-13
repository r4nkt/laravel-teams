<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use R4nkt\Teams\Events\AddingTeamMember;
use R4nkt\Teams\Events\TeamMemberAdded;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * members can create teams
 *  - events fired
 *  - they are the designated owner
 *  - attributes are set
 *  - ...?
 */
class AddTeamMemberTest extends TestCase
{
    /** @test */
    public function it_can_add_a_team_member()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        $role = 'some.role';
        $attributes = ['role' => $role];

        Teams::addTeamMember($owner, $team, $player, $attributes);

        // Team functionality
        $this->assertTrue($team->allMembers()->contains($player));
        $this->assertTrue($team->members->contains($player));
        $this->assertTrue($team->hasMember($player));
        $this->assertNotSame($team->owner->getKey(), $player->getKey());

        // Member functionality
        $this->assertTrue($player->allTeams()->contains($team));
        $this->assertFalse($player->ownedTeams->contains($team));
        $this->assertTrue($player->teams->contains($team));
        $this->assertFalse($player->ownsTeam($team));
        $this->assertTrue($player->belongsToTeam($team));

        // Events
        Event::assertDispatched(function (AddingTeamMember $event) use ($team, $player, $role) {
            return $event->membership->team_id === $team->id
                && $event->membership->member_id === $player->id
                && $event->membership->attributes['role'] === $role;
        });
        Event::assertDispatched(function (TeamMemberAdded $event) use ($team, $player, $role) {
            return $event->membership->team_id === $team->id
                && $event->membership->member_id === $player->id
                && $event->membership->attributes['role'] === $role;
        });
    }

    /** @test */
    public function it_cannot_add_a_nonexistant_team_member()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->make();

        try {
            $this->expectException(ValidationException::class);

            Teams::addTeamMember($owner, $team, $player);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('member_id', $e->errors());

            // Team functionality
            $this->assertFalse($team->allMembers()->contains($player));
            $this->assertFalse($team->members->contains($player));
            $this->assertFalse($team->hasMember($player));
            $this->assertNotSame($team->owner->getKey(), $player->getKey());

            // Member functionality
            $this->assertFalse($player->allTeams()->contains($team));
            $this->assertFalse($player->ownedTeams->contains($team));
            $this->assertFalse($player->teams->contains($team));
            $this->assertFalse($player->ownsTeam($team));
            $this->assertFalse($player->belongsToTeam($team));

            // Events
            Event::assertNotDispatched(AddingTeamMember::class);
            Event::assertNotDispatched(TeamMemberAdded::class);

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_add_team_member_that_is_already_on_team()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $player);

        Event::fake();

        try {
            $this->expectException(ValidationException::class);

            Teams::addTeamMember($owner, $team, $player);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('member_id', $e->errors());

            // Team functionality
            $this->assertTrue($team->allMembers()->contains($player));
            $this->assertTrue($team->members->contains($player));
            $this->assertTrue($team->hasMember($player));
            $this->assertNotSame($team->owner->getKey(), $player->getKey());

            // Member functionality
            $this->assertTrue($player->allTeams()->contains($team));
            $this->assertFalse($player->ownedTeams->contains($team));
            $this->assertTrue($player->teams->contains($team));
            $this->assertFalse($player->ownsTeam($team));
            $this->assertTrue($player->belongsToTeam($team));

            // Events
            Event::assertNotDispatched(AddingTeamMember::class);
            Event::assertNotDispatched(TeamMemberAdded::class);

            throw $e;
        }
    }
}
