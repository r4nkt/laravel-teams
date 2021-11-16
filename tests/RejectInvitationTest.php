<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use R4nkt\Teams\Events\RejectingInvitation;
use R4nkt\Teams\Events\InvitationRejected;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

class RejectInvitationTest extends TestCase
{
    /** @test */
    public function it_can_reject_a_team_invitation_if_invitee()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        Teams::rejectInvitation($prospect, $invitation);

        $this->assertSame(0, $team->invitations()->count());
        $this->assertSame(0, $prospect->receivedInvitations()->count());
        $this->assertSame(0, $owner->sentInvitations()->count());

        // Events
        Event::assertDispatched(function (RejectingInvitation $event) use ($invitation, $prospect) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $prospect->getKey();
        });
        Event::assertDispatched(function (InvitationRejected $event) use ($invitation, $prospect) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $prospect->getKey();
        });
    }

    /** @test */
    public function it_cannot_reject_a_team_invitation_if_not_owner_and_not_invitee()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonOwner = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $nonOwner);
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::rejectInvitation($nonOwner, $invitation);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_reject_a_team_invitation_if_owner()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::rejectInvitation($owner, $invitation);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_reject_a_team_invitation_if_not_team_member()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonTeamMember = Player::factory()->create();
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::rejectInvitation($nonTeamMember, $invitation);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }
}
