<?php

namespace App\Listeners;

use App\Actions\SyncProjects as SyncProjectsAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Registered;

class SyncProjects
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        SyncProjectsAction::run($event->user);
    }
}
