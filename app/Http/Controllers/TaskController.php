<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Access\Gate;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\SearchTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskListResource;
use App\Http\Resources\Task\TaskShowResource;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;

        return new TaskListResource(Task::paginate($per_page));
    }

    public function search(SearchTaskRequest $request)
    {
        $title = $request->title;
        $description = $request->description;
        $tags = $request->tags;
        $status = $request->status;
        $created_by_id = $request->created_by_id;
        $assigned_to_id = $request->assigned_to_id;
        $per_page = $request->per_page ?? 10;

        $projects = Task::with(['project_category', 'secteur'])->orderByDesc('created_at');

        if($title)
        {
            $projects = $projects->where('title', 'ILIKE', '%'.$title.'%')
                        ->orWhere('description', 'ILIKE', '%'.$title.'%')
                        ->orWhere('tags', 'ILIKE', '%'.$title.'%');
        }
        
        if($description)
        {
            $projects = $projects->where('description', 'ILIKE', '%'.$description.'%');
        }

        if($status)
        {
            $projects = $projects->currentStatus($status);
        }

        if($created_by_id)
        {
            $projects = $projects->where('created_by_id', $created_by_id);
        }

        if($assigned_to_id)
        {
            $projects = $projects->where('assigned_to_id', $assigned_to_id);
        }

        if($tags)
        {
            foreach($tags as $tag)
            {
                $projects = $projects->where('tags', 'ILIKE', '%'.$tag.'%');
            }
        }

        return TaskListResource::collection($projects->paginate($per_page));
    }

    public function store(StoreTaskRequest $request)
    {
        $project = Task::create($request->all());

        $project->setStatus($request->status ?? Task::STATUS_PENDING);

        if($request->attached_files != null)
        {
            foreach($request->attached_files as $file)
            {
                if($file != null)
                {
                    $project->addMedia($file)->toMediaCollection(Task::ATTACHED_FILES_MEDIA_COLLECTION);
                }
            }
        }

        return (new TaskShowResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Task $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TaskShowResource($project->load(['tasks']));
    }

    public function update(UpdateTaskRequest $request, Task $project)
    {
        $project->update($request->all());

        if ($request->status != null) {
            $project->setStatus($request->status);
        }

        if($request->attached_files != null)
        {
            (new MediaService)->updateMedias($request->attached_files, $request->medias_to_delete_ids, Task::ATTACHED_FILES_MEDIA_COLLECTION, $project);
        }

        return (new TaskShowResource($project->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Task $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function statuses()
    {
        return Task::STATUSES;
    }
}