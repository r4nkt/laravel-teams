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
    public function invite(BelongsToTeam $invokedBy, Team $team, BelongsToTeam $member, ?array $attributes = null): Invitation
    {
        Gate::forUser($invokedBy)->authorize('create', [Invitation::class, $team, $member, $attributes]);

        $this->validate($team, $member, $attributes);

        // InvitingTeamMember dispatched automatically via model...

        $invitation = $team->invitations()->create([
            'inviter_id' => $invokedBy->getKey(),
            'invitee_id' => $member->getKey(),
            'attributes' => $attributes,
        ]);

        // TeamMemberInvited dispatched automatically via model...

        return $invitation;
    }

    /**
     * Validate the invite member operation.
     */
    protected function validate(Team $team, BelongsToTeam $member, ?array $attributes): void
    {
        Validator::make([
            'team' => $team->getKey(),
            'member' => $member->getKey(),
            'attributes' => $attributes,
            'role' => $attributes['role'] ?? null,
        ], $this->rules($team, $attributes), [
            'member.unique' => __('This member has already been invited to the team.'),
        ])->after(
            $this->ensureMemberIsNotAlreadyOnTeam($team, $member)
        )->validateWithBag('inviteTeamMember');
    }

    /**
     * Get the validation rules for inviting a team member.
     */
    protected function rules(Team $team, ?array $attributes): array
    {
        return array_filter([
            'team' => 'required|exists:teams,id',
            'member' => [
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
    protected function ensureMemberIsNotAlreadyOnTeam(Team $team, BelongsToTeam $member): Closure
    {
        return function ($validator) use ($member, $team) {
            $validator->errors()->addIf(
                $team->hasMember($member),
                'member',
                __('This member already belongs to the team.')
            );
        };
    }
}
