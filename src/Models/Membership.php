<?php

namespace R4nkt\Teams\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use R4nkt\Teams\Events\AddingTeamMember;
use R4nkt\Teams\Events\TeamMemberAdded;
use R4nkt\Teams\Events\TeamMembershipDeleted;
use R4nkt\Teams\Events\TeamMembershipUpdated;

class Membership extends Pivot
{
    /** @var string */
    protected $table = 'member_team'; /** @todo Find out why this doesn't seem to matter. Rather, it must be explicitly provided in relationship definition. */

    /** @var bool */
    public $incrementing = true;

    /** @var array */
    protected $casts = [
        'attributes' => 'array',
    ];

    /** @var array */
    protected $dispatchesEvents = [
        'creating' => AddingTeamMember::class,
        'created' => TeamMemberAdded::class,
        'updated' => TeamMembershipUpdated::class,
        'deleted' => TeamMembershipDeleted::class,
    ];
}
