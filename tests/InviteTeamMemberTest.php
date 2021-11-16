<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use R4nkt\Teams\Events\InvitingTeamMember;
use R4nkt\Teams\Events\TeamMemberInvited;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * members can invite other members
 *  - events fired...?
 *  - multiple members can invite the same member
 *  - members can invite multiple members
 *  - ...?
 */
class InviteTeamMemberTest extends TestCase
{
    /** @test */
    public function it_allows_owners_to_invite_non_members_to_a_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();
        $role = 'some.role';

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect, ['role' => $role]);

        // Inviter, invitee, and team
        $this->assertSame(1, $owner->sentInvitations()->count());
        $this->assertSame(1, $prospect->receivedInvitations()->count());
        $this->assertSame(1, $team->invitations()->count());

        // Events
        Event::assertDispatched(function (InvitingTeamMember $event) use ($team, $owner, $prospect, $role) {
            return $event->invitation->team_id === $team->id
                && $event->invitation->inviter_id === $owner->getKey()
                && $event->invitation->invitee_id === $prospect->getKey()
                && $event->invitation->attributes['role'] === $role;
        });
        Event::assertDispatched(function (TeamMemberInvited $event) use ($invitation) {
            return $event->invitation->id === $invitation->id;
        });
    }

    /** @test */
    public function it_does_not_allow_other_team_members_to_invite_non_members_to_the_team()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $teamMember = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $teamMember);
        $prospect = Player::factory()->create();

        $this->expectException(AuthorizationException::class);

        Event::fake();

        try {
            Teams::inviteTeamMember($teamMember, $team, $prospect);
        } catch (AuthorizationException $e) {
            // Inviter, invitee, and team
            $this->assertSame(0, $owner->sentInvitations()->count());
            $this->assertSame(0, $teamMember->sentInvitations()->count());
            $this->assertSame(0, $prospect->receivedInvitations()->count());
            $this->assertSame(0, $team->invitations()->count());

            // Events
            Event::assertNotDispatched(InvitingTeamMember::class);
            Event::assertNotDispatched(TeamMemberInvited::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_a_non_member_to_be_invited_to_the_same_team_more_than_once()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(ValidationException::class);

        Event::fake();

        try {
            Teams::inviteTeamMember($owner, $team, $prospect);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('invitee', $e->errors());

            // Inviter, invitee, and team
            $this->assertSame(1, $owner->sentInvitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $team->invitations()->count());

            // Events
            Event::assertNotDispatched(InvitingTeamMember::class);
            Event::assertNotDispatched(TeamMemberInvited::class);

            throw $e;
        }
    }

    /** @test */
    public function it_does_not_allow_a_member_to_be_invited_to_a_team_to_which_they_already_belong()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $teamMember = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $teamMember);

        $this->expectException(ValidationException::class);

        Event::fake();

        try {
            Teams::inviteTeamMember($owner, $team, $teamMember);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('invitee', $e->errors());

            // Inviter, invitee, and team
            $this->assertSame(0, $owner->sentInvitations()->count());
            $this->assertSame(0, $teamMember->receivedInvitations()->count());
            $this->assertSame(0, $team->invitations()->count());

            // Events
            Event::assertNotDispatched(InvitingTeamMember::class);
            Event::assertNotDispatched(TeamMemberInvited::class);

            throw $e;
        }
    }

    /** @test */
    public function it_can_invite_a_non_member_to_multiple_teams()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $teamA = Teams::createTeam($owner, 'Team A');
        $teamB = Teams::createTeam($owner, 'Team B');
        $prospect = Player::factory()->create();

        $invitationA = Teams::inviteTeamMember($owner, $teamA, $prospect);
        $invitationB = Teams::inviteTeamMember($owner, $teamB, $prospect);

        // Inviter, invitee, and teams
        $this->assertSame(2, $owner->sentInvitations()->count());
        $this->assertSame(2, $prospect->receivedInvitations()->count());
        $this->assertSame(1, $teamA->invitations()->count());
        $this->assertSame(1, $teamB->invitations()->count());

        // Events
        Event::assertDispatched(InvitingTeamMember::class, 2);
        Event::assertDispatched(TeamMemberInvited::class, 2);
    }

    /** @test */
    public function it_does_not_allow_inviting_a_non_member_to_a_team_to_which_the_inviter_does_not_belong()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonTeamMember = Player::factory()->create();
        $prospect = Player::factory()->create();

        $this->expectException(AuthorizationException::class);

        try {
            Teams::inviteTeamMember($nonTeamMember, $team, $prospect);
        } catch (AuthorizationException $e) {
            // Inviter, invitee, and team
            $this->assertSame(0, $nonTeamMember->sentInvitations()->count());
            $this->assertSame(0, $prospect->receivedInvitations()->count());
            $this->assertSame(0, $team->invitations()->count());

            // Events
            Event::assertNotDispatched(InvitingTeamMember::class);
            Event::assertNotDispatched(TeamMemberInvited::class);

            throw $e;
        }
    }
}
