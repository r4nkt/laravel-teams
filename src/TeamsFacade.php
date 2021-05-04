<?php

namespace R4nkt\Teams;

use Illuminate\Support\Facades\Facade;

/**
 * @see \R4nkt\Teams\Teams
 */
class TeamsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-teams';
    }
}
