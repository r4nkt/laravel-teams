<?php

namespace R4nkt\Teams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Team;

class AddingTeamMember
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Team $team,
        public BelongsToTeam $member,
        public BelongsToTeam $invokedBy,
        public array $attributes,
    ) {}
}
