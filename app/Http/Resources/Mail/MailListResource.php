<?php

namespace App\Http\Resources\Mail;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
