<?php

namespace R4nkt\Teams\Actions;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Contracts\InvitesTeamMembers;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\Invitation;

class InviteTeamMember implements InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     */
    public function invite(BelongsToTeam $inviter, Team $team, BelongsToTeam $invitee, ?array $attributes = null): Invitation
    {
        Gate::forUser($inviter)->authorize('create', [Invitation::class, $team, $invitee, $attributes]);

        $this->validate($team, $invitee, $attributes);

        // InvitingTeamMember dispatched automatically via model...

        $invitation = $team->invitations()->create([
            'member_id' => $invitee->getKey(),
            'inviter_id' => $inviter->getKey(),
            'invitee_id' => $invitee->getKey(),
            'attributes' => $attributes,
        ]);

        // TeamMemberInvited dispatched automatically via model...

        return $invitation;
    }

    /**
     * Validate the invite member operation.
     */
    protected function validate(Team $team, BelongsToTeam $invitee, ?array $attributes): void
    {
        Validator::make([
            'team_id' => $team->getKey(),
            'invitee' => $invitee->getKey(),
            'attributes' => $attributes,
            'role' => $attributes['role'] ?? null,
        ], $this->rules($team, $attributes), [
            'invitee.unique' => __('This member has already been invited to the team.'),
        ])->after(
            $this->ensureMemberIsNotAlreadyOnTeam($team, $invitee)
        )->validateWithBag('inviteTeamMember');
    }

    /**
     * Get the validation rules for inviting a team member.
     */
    protected function rules(Team $team, ?array $attributes): array
    {
        return array_filter([
            'invitee' => [
                'required',
                'int',
                Rule::unique('team_invitations', 'invitee_id')
                    ->where(fn ($query) => $query->where('team_id', $team->getKey())),
            ],
            'attributes' => ['nullable', 'array'],
            'role' => ($attributes && array_key_exists('role', $attributes)) ? ['string'] : null,
        ]);
    }

    /**
     * Ensure that the member is not already on the team.
     */
    protected function ensureMemberIsNotAlreadyOnTeam(Team $team, BelongsToTeam $invitee): Closure
    {
        return function ($validator) use ($invitee, $team) {
            $validator->errors()->addIf(
                $team->hasMember($invitee),
                'invitee',
                __('This member already belongs to the team.')
            );
        };
    }
}
