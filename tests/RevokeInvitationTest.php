<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use R4nkt\Teams\Events\RevokingInvitation;
use R4nkt\Teams\Events\InvitationRevoked;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

class RevokeInvitationTest extends TestCase
{
    /** @test */
    public function it_can_revoke_a_team_invitation_if_owner()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        Teams::revokeInvitation($owner, $invitation);

        $this->assertSame(0, $team->invitations()->count());
        $this->assertSame(0, $prospect->receivedInvitations()->count());
        $this->assertSame(0, $owner->sentInvitations()->count());

        // Events
        Event::assertDispatched(function (RevokingInvitation $event) use ($invitation, $owner) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $owner->getKey();
        });
        Event::assertDispatched(function (InvitationRevoked $event) use ($invitation, $owner) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $owner->getKey();
        });
    }

    /** @test */
    public function it_cannot_revoke_a_team_invitation_if_not_owner()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $inviter = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $inviter);
        $notInviter = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $notInviter);
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::revokeInvitation($notInviter, $invitation);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_revoke_a_team_invitation_if_not_team_member()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonTeamMember = Player::factory()->create();
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($owner, $team, $prospect);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::revokeInvitation($nonTeamMember, $invitation);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }
}
