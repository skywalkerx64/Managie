<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Vérification d\'adresse mail')
                ->greeting('Bonjour cher '.$notifiable->fullname)
                ->line('Nous venons d\'enregistrer votre inscription sur la plateforme '.env('APP_NAME').' pour le compte de l\'entreprise '.$notifiable->profile->nom_etablissement.'.')
                ->line('Votre identifiant promoteur est : '.$notifiable->identity.'.')
                ->line('Cet identifiant vous sera exigé à chaque nouvelle demande et vous servira pour le suivit de vos demandes.')
                ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse mail.')
                ->action('Vérifier mon adresse mail', $url)
                ;
        });
    }
}
