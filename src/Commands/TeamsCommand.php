<?php

namespace R4nkt\Teams\Commands;

use Illuminate\Console\Command;

class TeamsCommand extends Command
{
    public $signature = 'laravel-teams';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
