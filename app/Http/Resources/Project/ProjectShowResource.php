<?php

namespace App\Http\Resources\Project;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectShowResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => (new Carbon($this->start_date))->format(config('panel.datetime_format')),
            'end_date' => (new Carbon($this->end_date))->format(config('panel.datetime_format')),
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'tasks' => $this->tasks,
            'status' => $this->status,
        ];
    }
}
