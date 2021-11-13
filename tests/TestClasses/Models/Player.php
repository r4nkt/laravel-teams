<?php

namespace R4nkt\Teams\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use R4nkt\Teams\Contracts\BelongsToTeam;
use R4nkt\Teams\Models\Concerns\HasTeams;

class Player extends Model implements BelongsToTeam
{
    use HasFactory;
    use HasTeams;
}
