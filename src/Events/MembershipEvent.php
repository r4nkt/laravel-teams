<?php

namespace R4nkt\Teams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use R4nkt\Teams\Models\Membership;

abstract class MembershipEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Membership $membership,
    ) {}
}
