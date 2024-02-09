<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'identity' => $this->identity,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'roles' => $this->roles,
            'signature' => $this->signature,
            'created_at' => (new Carbon ($this->created_at))->format(config('panel.datetime_format')),
            'email_verified_at' => (new Carbon ($this->email_verified_at))->format(config('panel.datetime_format')),
            'updated_at' => (new Carbon ($this->updated_at))->format(config('panel.datetime_format')),
        ];
    }
}
