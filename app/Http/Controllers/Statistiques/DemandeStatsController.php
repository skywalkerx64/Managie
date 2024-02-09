<?php

namespace App\Http\Controllers\Statistiques;

use App\Http\Controllers\Controller;
use App\Models\Demande\Demande;
use App\Models\Secteur;
use Illuminate\Http\Request;

class DemandeStatsController extends Controller
{
    public function counts()
    {
        $response = [];
        $statuses = [Demande::ACEPTED_STATUS, Demande::PENDING_STATUS, Demande::REJECTED_STATUS];

        $secteurs = Secteur::get(['id', 'title']);

        foreach ($statuses as $status)
        {
            $response[$status] = Demande::currentStatus($status)->count();
        }
        return response()->json($response);
    }

    public function counts_per_sector()
    {
        $response = [];
        $statuses = [Demande::ACEPTED_STATUS, Demande::PENDING_STATUS, Demande::REJECTED_STATUS];

        $secteurs = Secteur::get(['id', 'title', 'code']);
        foreach ($secteurs as $secteur) {

            $response[$secteur->code] = array_map(function($status) use (&$demandes, $secteur){

                return [$status => Demande::currentStatus($status)->whereHas('user', function($user) use ($secteur){

                    $user->whereHas('profile', function($profile) use ($secteur){

                        $profile->where('secteur_id', $secteur->id);
                    });
                })->count()];

            } , $statuses);
        }
        return response()->json($response);
    }

}
