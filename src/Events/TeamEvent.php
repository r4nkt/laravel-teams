<?php

namespace R4nkt\Teams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use R4nkt\Teams\Models\Team;

abstract class TeamEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Team $team,
    ) {}
}
