<?php

namespace R4nkt\Teams\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use R4nkt\Teams\Events\CreatingMembership;
use R4nkt\Teams\Events\DeletingMembership;
use R4nkt\Teams\Events\MembershipCreated;
use R4nkt\Teams\Events\MembershipDeleted;
use R4nkt\Teams\Events\MembershipUpdated;
use R4nkt\Teams\Events\UpdatingMembership;

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
        'created' => MembershipCreated::class,
        'creating' => CreatingMembership::class,
        'deleted' => MembershipDeleted::class,
        'deleting' => DeletingMembership::class,
        'updated' => MembershipUpdated::class,
        'updating' => UpdatingMembership::class,
    ];
}
