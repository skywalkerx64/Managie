<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        foreach($roles as $role)
        {
            $permission_ids = $permissions->map(function($permission) use($role){

                $default_roles = collect($permission->default_roles);

                if($default_roles->contains($role->alias))
                {
                    return $permission->id;
                }
            });
            $permission_ids = $permission_ids->filter(function($value){
                return $value != null;
            });
            $role->permissions()->attach($permission_ids);
        }
    }
  
}
