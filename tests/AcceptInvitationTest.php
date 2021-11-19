<?php

namespace R4nkt\Teams\Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use R4nkt\Teams\Events\AcceptingInvitation;
use R4nkt\Teams\Events\InvitationAccepted;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Teams;
use R4nkt\Teams\Tests\TestClasses\Models\Player;

/**
 * invitees can accept invitations
 *  - events fired...?
 *  - ignored_at being set for any other "open" invitations for the same team
 *  - accepted_at being set
 *  - ...?
 */
class AcceptInvitationTest extends TestCase
{
    /** @test */
    public function it_can_accept_a_team_invitation_if_invitee()
    {
        Event::fake();

        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();
        $role = 'some.role';
        $attributes = ['role' => $role];

        $invitation = Teams::inviteTeamMember($team, $prospect, $owner, $attributes);

        Teams::acceptInvitation($invitation, $prospect);

        $team->refresh();

        $this->assertSame(0, $team->invitations()->count());
        $this->assertSame(0, $prospect->receivedInvitations()->count());
        $this->assertSame(0, $owner->sentInvitations()->count());

        // Team functionality
        $this->assertTrue($team->allMembers()->contains($prospect));
        $this->assertTrue($team->members->contains($prospect));
        $this->assertTrue($team->hasMember($prospect));
        $this->assertSame(0, $team->invitations()->count());

        // Member functionality
        $this->assertTrue($prospect->allTeams()->contains($team));
        $this->assertFalse($prospect->ownedTeams->contains($team));
        $this->assertTrue($prospect->teams->contains($team));
        $this->assertFalse($prospect->ownsTeam($team));
        $this->assertTrue($prospect->belongsToTeam($team));
        $this->assertSame($role, $prospect->teams()->first()->membership->attributes['role']);

        // Events
        Event::assertDispatched(function (AcceptingInvitation $event) use ($invitation, $prospect) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $prospect->getKey();
        });
        Event::assertDispatched(function (InvitationAccepted $event) use ($invitation, $prospect) {
            return $event->invitation->id === $invitation->id
                && $event->invokedBy->getKey() === $prospect->getKey();
        });
    }

    /** @test */
    public function it_cannot_accept_a_team_invitation_if_not_owner_and_not_invitee()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonOwner = Player::factory()->create();
        Teams::addTeamMember($owner, $team, $nonOwner);
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($team, $prospect, $owner);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::acceptInvitation($invitation, $nonOwner);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_accept_a_team_invitation_if_owner()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($team, $prospect, $owner);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::acceptInvitation($invitation, $owner);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }

    /** @test */
    public function it_cannot_accept_a_team_invitation_if_not_team_member()
    {
        $owner = Player::factory()->create();
        $team = Teams::createTeam($owner, 'Test Team');
        $nonTeamMember = Player::factory()->create();
        $prospect = Player::factory()->create();

        $invitation = Teams::inviteTeamMember($team, $prospect, $owner);

        $this->expectException(AuthorizationException::class);

        try {
            Teams::acceptInvitation($invitation, $nonTeamMember);
        } catch (AuthorizationException $e) {
            $this->assertSame(1, $team->invitations()->count());
            $this->assertSame(1, $prospect->receivedInvitations()->count());
            $this->assertSame(1, $owner->sentInvitations()->count());

            throw $e;
        }
    }
}
