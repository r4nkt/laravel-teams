<?php

namespace R4nkt\Teams\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use R4nkt\Teams\Models\Team;
use R4nkt\Teams\Models\Invitation;
use R4nkt\Teams\Policies\InvitationPolicy;
use R4nkt\Teams\Policies\TeamPolicy;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Event::class => [
        //     Listener::class,
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Invitation::class, InvitationPolicy::class);
    }
}
