<?php

namespace R4nkt\Teams\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use R4nkt\Teams\Events\InvitingTeamMember;
use R4nkt\Teams\Events\TeamMemberInvited;
use R4nkt\Teams\Teams;

class Invitation extends Model
{
    /** @var string */
    protected $table = 'team_invitations';

    /** @var array */
    protected $fillable = [
        'inviter_id',
        'invitee_id',
        'attributes',
    ];

    /** @var array */
    protected $casts = [
        'attributes' => 'array',
    ];

    /** @var array */
    protected $dispatchesEvents = [
        'creating' => InvitingTeamMember::class,
        'created' => TeamMemberInvited::class,
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get member that created the invitation.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(Teams::memberModel(), 'inviter_id');
    }

    /**
     * Get the member for which the invitation is intended.
     */
    public function invitee(): BelongsTo
    {
        return $this->belongsTo(Teams::memberModel(), 'invitee_id');
    }
}
