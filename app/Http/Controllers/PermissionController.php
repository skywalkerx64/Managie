<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\ManagePermissionRequest;
use App\Http\Requests\Permission\SearchPermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\Permission\PermissionListResource;
use App\Http\Resources\Permission\PermissionShowResource;
use App\Http\Requests\Permission\ManageRolePermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PermissionListResource(Permission::paginate(10));
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->all());

        return (new PermissionShowResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Permission $permission)
    {
        abort_if(Gate::denies('permission_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PermissionShowResource($permission);
    }

    public function search(SearchPermissionRequest $request)
    {
        $permissions = Permission::query()->orderByDesc('created_at');

        $only_active = $request->only_active ?? true;

        $per_page = $request->per_page ?? 10;

        $role_id = $request->role_id;

        $module = $request->module;

        $action = $request->action;

        $resource = $request->resource;

        $description = $request->description;

        $user_id = $request->user_id;

        if($user_id != null)
        {
            $user = User::find($user_id);

            if($user != null)
            {
                $permissions = $user->permissions();
            }
        }

        if($role_id != null)
        {
            $permissions = $permissions->whereHas('roles', function($role) use($role_id){
                $role->where('id', $role_id);
            });
        }

        if($module != null)
        {
            $permissions = $permissions->where('module', 'ILIKE', '%'.$module.'%')
                                        ->orWhere('title', 'ILIKE', '%'.$module.'%')
                                        ->orWhere('description', 'ILIKE', '%'.$module.'%');
        }

        if($description != null)
        {
            $permissions = $permissions->where('description', 'ILIKE', '%'.$description.'%');
        }

        if($action != null)
        {
            $permissions = $permissions->where('action', 'ILIKE', '%'.$action.'%');
        }

        if($resource != null)
        {
            $permissions = $permissions->where('resource', 'ILIKE', '%'.$resource.'%');
        }

        if($only_active)
        {
            if($user_id != null)
            {
                $permissions = $permissions->wherePivot('is_active', true);
            }
            else
            {
                $permissions = $permissions->where('is_active', true);
            }
        }

        return (PermissionListResource::collection($permissions->orderBy('id')->paginate($per_page)));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->all());

        return (new PermissionShowResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Permission $permission)
    {
        abort_if(Gate::denies('permission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permission->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function manage(ManagePermissionRequest $request)
    {
        $user = User::find($request->user_id);

        $action = $request->action;
        $permission_ids = $request->permission_ids;

        switch ($action) {
            case Permission::ACTION_GRANT:
                foreach($permission_ids as $permission_id)
                {
                    $already_attached = $user->permissions()->where('id', $permission_id)->first();
                    if($already_attached != null)
                    {
                        $already_attached->pivot->update(['is_active' => true]);
                    }
                    else
                    {
                        $user->permissions()->attach($permission_id);
                    }
                }
                break;
            case Permission::ACTION_REVOKE:
                $user->permissions()->detach($permission_ids);
                break;
            
            default:
                
                break;
        }
    }

    public function role_manage(ManageRolePermissionRequest $request)
    {
        $role = Role::find($request->role_id);

        $permission_ids = $request->permission_ids;

        $permissions = $role->permissions()->whereIn('id', $permission_ids)->get();

        foreach($permissions as $permission)
        {
            $permission->pivot->update([
                "is_active" => $request->is_active
            ]);
        }

        return (PermissionListResource::collection($role->permissions->sortBy('id')))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
