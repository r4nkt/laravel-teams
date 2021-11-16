<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use R4nkt\Teams\Events\CreatingTeam;
use R4nkt\Teams\Events\DeletingMembership;
use R4nkt\Teams\Events\MembershipDeleted;
use R4nkt\Teams\Events\RemovingTeamMember;
use R4nkt\Teams\Events\TeamCreated;
use R4nkt\Teams\Events\TeamMemberRemoved;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * members can be removed from teams
 *  - events fired...?
 *  - ...?
 */
class RemoveTeamMemberTest extends TestCase
{
    /** @test */
    public function it_allows_a_member_to_be_removed_from_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $player);

        Teams::removeTeamMember($owner, $team, $player);

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
        Event::assertDispatched(function (RemovingTeamMember $event) use ($team, $player, $owner) {
            return $event->team->id === $team->id
                && $event->member->getKey() === $player->getKey()
                && $event->invokedBy->getKey() === $owner->getKey();
        });
        Event::assertDispatched(function (TeamMemberRemoved $event) use ($team, $player, $owner) {
            return $event->team->id === $team->id
                && $event->member->getKey() === $player->getKey()
                && $event->invokedBy->getKey() === $owner->getKey();
        });
        Event::assertDispatched(function (DeletingMembership $event) use ($team, $player, $owner) {
            return $event->membership->team_id === $team->id
                && $event->membership->member_id === $player->getKey();
        });
        Event::assertDispatched(function (MembershipDeleted $event) use ($team, $player, $owner) {
            return $event->membership->team_id === $team->id
                && $event->membership->member_id === $player->getKey();
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
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

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
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

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
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }
}
