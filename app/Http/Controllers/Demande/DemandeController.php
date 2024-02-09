<?php

namespace App\Http\Controllers\Demande;

use App\Models\Role;
use App\Models\User;
use App\Models\TypeDemande;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Models\Demande\Demande;
use App\Models\TypeAutoEvaluation;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Events\Demande\DemandeCreated;
use App\Events\Demande\DemandeAccepted;
use App\Events\Demande\DemandeRejected;
use Symfony\Component\HttpFoundation\Response;
use App\Events\Demande\AuditorAssociatedToDemande;
use App\Http\Requests\Demande\UpdateDemandeRequest;
use App\Http\Resources\Demande\DemandeListResource;
use App\Http\Resources\Demande\DemandeShowResource;
use App\Http\Requests\Demande\SearchDemandeRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DemandeController extends Controller
{
    public function search(SearchDemandeRequest $request)
    {
        $per_page = $request->per_page ?? 10;

        $identity = $request->identity;

        $user_id = $request->user_id;

        $auditor_id = $request->auditor_id;

        $technical_visitor_id = $request->technical_visitor_id;

        $treatment_agent_id = $request->treatment_agent_id;
        
        $secteur_id = $request->secteur_id;

        $type_demande_id = $request->type_demande_id;

        $reference = $request->reference;

        $numero_demande = $request->numero_demande;

        $code_demande = $request->code_demande;

        $hasnt_audit = $request->hasnt_audit;

        $hasnt_evaluation = $request->hasnt_evaluation;

        $periode = $request->periode;

        $status = $request->status;

        $statuses = $request->statuses;

        $urgence_status = $request->urgence_status;

        $demandes = Demande::with(['type_demande', 'user', 'statuses.user', 'media'])->orderByDesc('created_at');


        if($user_id)
        {
            $demandes = $demandes->where('user_id', $user_id);
        }

        if($auditor_id)
        {
            $demandes = $demandes->where('auditor_id', $auditor_id);
        }

        if($technical_visitor_id)
        {
            $demandes = $demandes->where('technical_visitor_id', $technical_visitor_id);
        }

        if($treatment_agent_id)
        {
            $demandes = $demandes->where('treatment_agent_id', $treatment_agent_id);
        }

        if($secteur_id)
        {
            $demandes = $demandes->whereHas('type_demande', function ($type_demande) use ($secteur_id) {

                $type_demande->where('secteur_id', $secteur_id);

            });
        }

        if($type_demande_id)
        {
            $demandes = $demandes->where('type_demande_id', $type_demande_id);
        }

        if($identity)
        {
            $demandes = $demandes->where('data->identity', $identity);
        }

        if($reference)
        {
            $demandes = $demandes->where('data->reference', $reference);
        }

        if($numero_demande)
        {
            $demandes = $demandes->where('data->numero_demande', $numero_demande);
        }

        if($code_demande)
        {
            $demandes = $demandes->where('data->code_demande', $code_demande);
        }

        if($periode)
        {
            $demandes = $demandes->whereBetween('created_at', [$periode['from'], $periode['to']]);
        }

        if($hasnt_audit)
        {
            $demandes = $demandes->whereDoesntHave('auto_evaluations', function ($auto_evaluation) {

                $auto_evaluation->whereHas('type_auto_evaluation', function ($type_auto_evaluation) {

                    $type_auto_evaluation->where('type', TypeAutoEvaluation::EXAMEN_TYPE);

                });
            });
        }

        if($hasnt_evaluation)
        {
            $demandes = $demandes->whereDoesntHave('auto_evaluations', function ($auto_evaluation) {

                $auto_evaluation->whereHas('type_auto_evaluation', function ($type_auto_evaluation) {

                    $type_auto_evaluation->where('type', TypeAutoEvaluation::EVALUATON_TYPE);

                });
            });
        }

        if($status)
        {
            $demandes = $demandes->currentStatus($status);
        }

        if($statuses)
        {
            $demandes = $demandes->currentStatus($statuses);
        }

        if($urgence_status)
        {
            $demandes = $demandes->orderByDesc('id')->get()->filter(function ($demande) use ($urgence_status) {

                $type_demande = $demande->type_demande;

                $current_status = $demande->status();

                $duration = collect($type_demande->notification_durations)->where('status', $current_status->name)->first();

                if($duration)
                {
                    $current_duration = now()->diffInDays($current_status->created_at);
                    switch ($urgence_status) {

                        case Demande::LOW_URGENCE_STATE :

                            if($current_duration >= 1/3 * $duration['duration'])
                            {
                                return true;
                            }
                            return false;
                            break;

                        case Demande::MEDIUM_URGENCE_STATE :

                            if($current_duration >= 2/3 * $duration['duration'])
                            {
                                return true;
                            }
                            return false;
                            break;

                        case Demande::HIGH_URGENCE_STATE :

                            if($current_duration >= $duration['duration'])
                            {
                                return true;
                            }
                            return false;
                            break;
                    }
                }
            });
        }

        return DemandeListResource::collection($demandes->paginate($per_page));
    }

    public function show(Demande $demande)
    {
        if(!$this->can_show($demande))
        {
            return response()->json(['message' => 'Not allowed'], Response::HTTP_FORBIDDEN);
        }

        return new DemandeShowResource($demande->load('user', 'type_demande', 'statuses.user', 'auditor', 'technical_visitor'));
    }

    public function update(UpdateDemandeRequest $request, Demande $demande)
    {
        Log::debug($request);
        
        $auth_user = User::find(auth()->user()->id);

        // if(!$this->can_update($demande, $auth_user))
        // {
        //     return response()->json(['message' => 'Not allowed'], Response::HTTP_FORBIDDEN);
        // }

        if($demande->treatment_agent_id == null)
        {
            $demande->update(['treatment_agent_id' => auth()->user()->id]);
        }

        if($request->treatment_agent_id)
        {
            if(!$auth_user->HasRoles([Role::INTERN_ADMIN_ROLE_ALIAS]))
            {
                return response()->json(['message' => 'Not allowed'], Response::HTTP_FORBIDDEN);
            }
        }

        if(!$this->can_treat($demande, $auth_user))
        {
            return response()->json(['message' => 'Not allowed'], Response::HTTP_FORBIDDEN);
        }
        
        $demande->update($request->all());

        if($request->data)
        {
            $demande->update(['data' => $request->data]);
        }

        $additional_data = [];

        if($request->identity)
        {
            $additional_data['user_id'] = User::where('identity', $request->identity)->first()?->id;
        }

        if($request->code_demande)
        {
            $additional_data['type_demande_id'] = TypeDemande::where('code', $request->code_demande)->first()?->id;
        }

        if($request->auditor_id)
        {
            AuditorAssociatedToDemande::dispatch($demande);
        }

        $demande->update($additional_data);

        if($request->attached_files != null)
        {
            (new MediaService)->updateMedias($request->attached_files, $request->medias_to_delete_ids, Demande::FILES_COLLECTION_NAME, $demande);
        }

        if($request->checklist_signature_file)
        {
            if(in_array(gettype($request->checklist_signature_file), ['integer', 'string']))
            {
                $existing_media = Media::find($request->checklist_signature_file)?->getPath();

                if($existing_media == null)
                {
                    return response("Specified media not found", Response::HTTP_NOT_FOUND);
                }
                $demande->clearMediaCollection(Demande::SIGNATURE_COLLECTION_NAME);
                
                $demande->addMedia($existing_media)->toMediaCollection(Demande::SIGNATURE_COLLECTION_NAME);
            }
            else
            {
                $demande->clearMediaCollection(Demande::SIGNATURE_COLLECTION_NAME);
                $demande->addMediaFromRequest('checklist_signature_file')->toMediaCollection(Demande::SIGNATURE_COLLECTION_NAME);
            }
        }

        if($request->status)
        {
            $demande->setStatus($request->status, null, auth()->user()->id);
            switch ($request->status) {
                case Demande::ACEPTED_STATUS:

                    DemandeAccepted::dispatch($demande);
                    break;
                    
                case Demande::REJECTED_STATUS:

                    DemandeRejected::dispatch($demande);
                    break;
            }
        }

        return new DemandeShowResource($demande->load('user', 'type_demande', 'statuses.user'));
    }
    public function store(Request $request)
    {
        Log::debug($request->all());
        
        $demande = Demande::create([
            'reference' => Str::random(20),
            'data' => $request->all(),
    ]);

        $additional_data = [];

        if($request->identity)
        {
            $additional_data['user_id'] = User::where('identity', $request->identity)->first()?->id;
        }

        if($request->code_demande)
        {
            $additional_data['type_demande_id'] = TypeDemande::where('code', $request->code_demande)->first()?->id;
        }

        $additional_data['check_object'] = array_map(function($item) {
            return null;
        } ,$request->all());

        $demande->update($additional_data);

        if($request->attached_files != null)
        {
            foreach($request->attached_files as $file)
            {
                if($file != null)
                {
                    $demande->addMedia($file)->toMediaCollection(Demande::FILES_COLLECTION_NAME);
                }
            }
        }


        $demande->setStatus($request->status ?? Demande::PENDING_STATUS);

        DemandeCreated::dispatch($demande);

        return response()->json('Demande enregistrée');
    }

    public function destroy(Demande $demande)
    {
        abort_if(!$this->is_allowed($demande), Response::HTTP_FORBIDDEN);
        $demande->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function truncate()
    {
        abort_if(Gate::denies('demande_delete'), Response::HTTP_FORBIDDEN);
        
        foreach (Demande::all() as $demande) {
            $demande->delete();
        }
    }

    //Controls
    public function can_show(Demande $demande, int $user_id = null)
    {
        $user = User::find($user_id ?? auth()->user()->id);

        return $user->HasRoles([Role::ADMIN_ROLE_ALIAS, Role::INTERN_ADMIN_ROLE_ALIAS, Role::INTERN_AGENT_ROLE_ALIAS, Role::AUDITOR_USER_ROLE_ALIAS]);
    }

    public function can_treat(Demande $demande, User $user = null)
    {
        $user = $user ?? User::find(auth()->user()->id);

        return $user->HasRoles([Role::INTERN_ADMIN_ROLE_ALIAS, Role::INTERN_AGENT_ROLE_ALIAS]) || $demande->treatment_agent_id == $user->id || $demande->user_id == $user->id ;
    }

    public function can_update(Demande $demande, User $user = null)
    {
        $user = $user ?? User::find(auth()->user()->id);

        return $demande->user_id == $user->id || $this->can_treat($demande, $user);
    }

    public function can_update_treatment_user(Demande $demande, User $user = null)
    {
        $user = $user ?? User::find(auth()->user()->id);

        return $user->HasRoles([Role::INTERN_ADMIN_ROLE_ALIAS, Role::INTERN_AGENT_ROLE_ALIAS]) && $demande->treatment_agent_id == $user->id;
    }

}
