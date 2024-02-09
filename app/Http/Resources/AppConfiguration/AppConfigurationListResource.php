<?php

namespace App\Http\Resources\AppConfiguration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppConfigurationListResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ];
    }
}
