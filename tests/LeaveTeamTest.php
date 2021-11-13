<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use R4nkt\Teams\Events\CreatingTeam;
use R4nkt\Teams\Events\MemberLeavingTeam;
use R4nkt\Teams\Events\MemberLeftTeam;
use R4nkt\Teams\Events\TeamCreated;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * members can leave teams
 *  - events fired...?
 *  - ...?
 */
class LeaveTeamTest extends TestCase
{
    /** @test */
    public function it_allows_a_member_to_leave_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $player);

        Teams::leaveTeam($player, $team);

        // Team functionality
        $this->assertFalse($team->allMembers()->contains($player));
        $this->assertFalse($team->members->contains($player));
        $this->assertFalse($team->hasMember($player));

        // Member functionality
        $this->assertFalse($player->allTeams()->contains($team));
        $this->assertFalse($player->ownedTeams->contains($team));
        $this->assertFalse($player->teams->contains($team));
        $this->assertFalse($player->ownsTeam($team));
        $this->assertFalse($player->belongsToTeam($team));

        // Events
        Event::assertDispatched(function (MemberLeavingTeam $event) use ($team, $player) {
            return $event->team->id === $team->id
                && $event->member->getKey() === $player->getKey();
        });
        Event::assertDispatched(function (MemberLeftTeam $event) use ($team, $player) {
            return $event->team->id === $team->id
                && $event->member->getKey() === $player->getKey();
        });
    }

    /** @test */
    public function it_does_not_allow_non_members_to_leave_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();

        $this->expectException(AuthorizationException::class);

        try {
            Teams::leaveTeam($player, $team);
        } catch (AuthorizationException $e) {
            Event::assertNotDispatched(MemberLeavingTeam::class);
            Event::assertNotDispatched(MemberLeftTeam::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_the_owner_to_leave_a_team_that_has_no_other_members()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');

        $this->expectException(AuthorizationException::class);

        try {
            Teams::leaveTeam($owner, $team);
        } catch (AuthorizationException $e) {
            Event::assertNotDispatched(MemberLeavingTeam::class);
            Event::assertNotDispatched(MemberLeftTeam::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_the_owner_to_leave_a_team_that_has_other_members()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $player);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::leaveTeam($owner, $team);
        } catch (AuthorizationException $e) {
            Event::assertNotDispatched(MemberLeavingTeam::class);
            Event::assertNotDispatched(MemberLeftTeam::class);

            throw $e;
        }
    }
}
