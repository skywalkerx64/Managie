<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppConfiguration;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\AppConfiguration\StoreAppConfigurationRequest;
use App\Http\Requests\AppConfiguration\SearchAppConfigurationRequest;
use App\Http\Requests\AppConfiguration\UpdateAppConfigurationRequest;
use App\Http\Resources\AppConfiguration\AppConfigurationListResource;
use App\Http\Resources\AppConfiguration\AppConfigurationShowResource;

class AppConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;

        return AppConfigurationListResource::collection(AppConfiguration::orderByDesc('created_at')->paginate($per_page));
    }

    public function search(SearchAppConfigurationRequest $request)
    {
        $code = $request->code;
        $per_page = $request->per_page ?? 10;

        $app_configurations = AppConfiguration::orderByDesc('created_at');

        if($code)
        {
            $app_configurations = $app_configurations->where('code', 'ILIKE', '%'.$code.'%');
        }

        return AppConfigurationListResource::collection($app_configurations->paginate($per_page));
    }

    public function store(StoreAppConfigurationRequest $request)
    {
        $app_configuration = AppConfiguration::create($request->all());

        return (new AppConfigurationShowResource($app_configuration))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AppConfiguration $app_configuration)
    {
        return new AppConfigurationShowResource($app_configuration);
    }

    public function update(UpdateAppConfigurationRequest $request, AppConfiguration $app_configuration)
    {
        $app_configuration->update($request->all());

        return (new AppConfigurationShowResource($app_configuration->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AppConfiguration $app_configuration)
    {
        abort_if(Gate::denies('app_configuration_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $app_configuration->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
