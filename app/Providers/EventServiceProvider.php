<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Events\Demande\DemandeAccepted;
use App\Events\Demande\DemandeRejected;
use App\Listeners\Demande\HandleDemandeCreated;
use App\Listeners\Demande\HandleDemandeAccepted;
use App\Listeners\Demande\HandleDemandeRejected;
use App\Events\Demande\AuditorAssociatedToDemande;
use App\Listeners\Demande\SendDemandeMailToAuditor;
use App\Listeners\InitializePermissionsAfterRegister;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            InitializePermissionsAfterRegister::class,
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
