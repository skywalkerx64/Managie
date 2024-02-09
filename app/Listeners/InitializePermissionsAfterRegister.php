<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InitializePermissionsAfterRegister
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
    public function handle(object $event): void
    {
        $user = $event->user;
        $user_permission_ids = $user->roles()->with('permissions')->get()->pluck('permissions.*.id')->flatten();
        $user->permissions()->attach($user_permission_ids);
    }
}
