<?php

namespace App\Http\Middleware;

use App\Models\AppConfiguration;
use App\Models\Permission;
use Closure;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        if(auth()->user() != null)
        {
            $user = User::find(auth()->user()->id);
            if(AppConfiguration::getByCode(Permission::GRANT_PER_USER_CONF_CODE)->value)
            {
                $permissions = $user->permissions()->wherePivot('is_active', true)->get();
            }
            else
            {
                $permissions = Permission::whereHas('roles', function($role) use($user){
                    $role->whereIn('id', $user->roles->pluck('id'))->where('is_active', true);
                })->get();;
            }
            foreach($permissions as $permission)
            {
                Gate::define($permission->title, function () use(&$permission){
                    return $permission != null;
                });
            }
        }
        return $next($request);
    }
}
