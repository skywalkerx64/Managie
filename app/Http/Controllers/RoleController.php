<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\ManageRoleRequest;
use App\Http\Requests\Role\SearchRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Role\RoleListResource;
use App\Http\Resources\Role\RoleShowResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Permission\PermissionListResource;
use App\Http\Requests\Role\SearchPermissionPerRoleRequest;

class RoleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleListResource(Role::orderByDesc('created_at')->with(['permissions', 'users'])->get());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        $role->users()->sync($request->input('users', []));

        return (new RoleShowResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function search(SearchRoleRequest $request)
    {
        $roles = Role::with(['permissions'])->orderByDesc('created_at');

        $title = $request->title;
        $alias =$request->alias;
        $description =$request->description;
        $per_page = $request->per_page ?? 10;

        if($title)
        {
            $roles = $roles->where('title', 'ILIKE', '%'.$title.'%');
        }

        if($alias)
        {
            $roles = $roles->where('alias', 'ILIKE', '%'.$alias.'%');
        }

        if($description)
        {
            $roles = $roles->where('description', 'ILIKE', '%'.$description.'%');
        }

        return RoleListResource::collection($roles->get());
    }

    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleShowResource($role->load(['permissions', 'users']));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        $role->users()->sync($request->input('users', []));

        return (new RoleShowResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function permission_manage(ManageRoleRequest $request)
    {
        $role = Role::find($request->role_id);

        $action = $request->action;
        $permission_ids = $request->permission_ids;
        $permissions = $role->permissions()->whereIn('id', $permission_ids)->get();

        switch ($action) {
            case Permission::ACTION_GRANT:
                foreach($permission_ids as $permission_id)
                {
                    $already_attached = $role->permissions()->where('id', $permission_id)->first();
                    if($already_attached != null)
                    {
                        $already_attached->pivot->update(['is_active' => true]);
                    }
                    else
                    {
                        $role->permissions()->attach($permission_id);
                    }
                }
                break;
            case Permission::ACTION_REVOKE:
                $role->permissions()->detach($permission_ids);
                break;
            
            case Permission::ACTION_ACTIVATE:
                foreach($permissions as $permission)
                {
                    $permission->pivot->update(['is_active' => true]);
                }
                break;
            case Permission::ACTION_DESACTIVATE:
                foreach($permissions as $permission)
                {
                    $permission->pivot->update(['is_active' => false]);
                }
                break;

            default:
                
                break;
        }
        return (PermissionListResource::collection($role->permissions->sortBy('created_at')))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function permissions(Role $role, SearchPermissionPerRoleRequest $request)
    {
        $permissions = $role->permissions()->orderByDesc('created_at');

        $only_active = $request->only_active ?? false;

        $per_page = $request->per_page ?? 10;

        $module = $request->module;

        $action = $request->action;

        $resource = $request->resource;

        $description = $request->description;

        $editables = $request->editables ?? true;

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
            $permissions = $permissions->where('permissions.is_active', true);
        }

        if($editables)
        {
            $permissions = $permissions->where("module", "!=", null)->where("description", "!=", null);
        }

        return (PermissionListResource::collection($permissions->orderBy('id')->paginate($per_page)));
    }
}
