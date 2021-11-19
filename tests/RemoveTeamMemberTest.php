<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
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
    public function it_allows_a_member_to_be_removed_from_the_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($team, $player, $owner);

        Teams::removeTeamMember($team, $player, $owner);

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
    public function it_does_not_allow_a_non_member_to_be_removed_from_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();

        $this->expectException(ValidationException::class);

        try {
            Teams::removeTeamMember($team, $player, $owner);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('member', $e->errors());

            Event::assertNotDispatched(RemovingTeamMember::class);
            Event::assertNotDispatched(TeamMemberRemoved::class);
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_a_non_member_to_remove_a_member_from_the_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($team, $player, $owner);
        $nonTeamMember = Player::factory()->create();

        $this->expectException(AuthorizationException::class);

        try {
            Teams::removeTeamMember($team, $player, $nonTeamMember);
        } catch (AuthorizationException $e) {
            Event::assertNotDispatched(RemovingTeamMember::class);
            Event::assertNotDispatched(TeamMemberRemoved::class);
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_an_unauthorized_member_to_remove_another_member_from_the_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($team, $player, $owner);
        $unauthorizedTeamMember = Player::factory()->create();
        Teams::addTeamMember($team, $unauthorizedTeamMember, $owner);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::removeTeamMember($team, $player, $unauthorizedTeamMember);
        } catch (AuthorizationException $e) {
            Event::assertNotDispatched(RemovingTeamMember::class);
            Event::assertNotDispatched(TeamMemberRemoved::class);
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_an_authorized_member_to_remove_himself_from_a_team_that_has_other_members()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $player = Player::factory()->create();
        Teams::addTeamMember($team, $player, $owner);

        $this->expectException(ValidationException::class);

        try {
            Teams::removeTeamMember($team, $owner, $owner);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('member', $e->errors());

            Event::assertNotDispatched(RemovingTeamMember::class);
            Event::assertNotDispatched(TeamMemberRemoved::class);
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_an_authorized_member_to_remove_himself_from_a_team_that_has_no_other_members()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');

        $this->expectException(ValidationException::class);

        try {
            Teams::removeTeamMember($team, $owner, $owner);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('member', $e->errors());

            Event::assertNotDispatched(RemovingTeamMember::class);
            Event::assertNotDispatched(TeamMemberRemoved::class);
            Event::assertNotDispatched(DeletingMembership::class);
            Event::assertNotDispatched(MembershipDeleted::class);

            throw $e;
        }
    }
}
