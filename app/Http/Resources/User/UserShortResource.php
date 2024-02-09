<?php

namespace App\Http\Resources\User;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AppConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if(AppConfiguration::getByCode(Permission::GRANT_PER_USER_CONF_CODE)->value)
        {
            $permissions = $this->permissions->where('pivot.is_active', true);
        }
        else
        {
            $permissions = Permission::whereHas('roles', function($role){
                $role->whereIn('id', $this->roles->pluck('id'))->where('is_active', true);
            })->get();
        }
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'identity' => $this->identity,
            'can_login' => $this->can_login,
            'roles' => $this->roles,
            'profile' => $this->profile,
            'signature' => $this->signature,
            'email_verified_at' => (new Carbon ($this->email_verified_at))->format(config('panel.datetime_format')),
            'created_at' => (new Carbon ($this->created_at))->format(config('panel.datetime_format')),
            'updated_at' => (new Carbon ($this->updated_at))->format(config('panel.datetime_format')),
            'permissions' => $permissions->map(function($permission){
                return [
                    "action" => $permission['action'],
                    "resource" => $permission['resource'],
                ];
            })
        ];
    }
}
