<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\SearchProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\Project\ProjectListResource;
use App\Http\Resources\Project\ProjectShowResource;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;

        return new ProjectListResource(Project::paginate($per_page));
    }

    public function search(SearchProjectRequest $request)
    {
        $title = $request->title;
        $description = $request->description;
        $tags = $request->tags;
        $status = $request->status;
        $created_by_id = $request->created_by_id;
        $per_page = $request->per_page ?? 10;

        $projects = Project::with(['project_category', 'secteur'])->orderByDesc('created_at');

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

        if($created_by_id)
        {
            $projects = $projects->where('created_by_id', $created_by_id);
        }

        if($status)
        {
            $projects = $projects->currentStatus($status);
        }

        if($tags)
        {
            foreach($tags as $tag)
            {
                $projects = $projects->where('tags', 'ILIKE', '%'.$tag.'%');
            }
        }

        return ProjectListResource::collection($projects->paginate($per_page));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->all());

        $project->setStatus($request->status ?? Project::STATUS_PENDING);

        if($request->attached_files != null)
        {
            foreach($request->attached_files as $file)
            {
                if($file != null)
                {
                    $project->addMedia($file)->toMediaCollection(Project::ATTACHED_FILES_MEDIA_COLLECTION);
                }
            }
        }

        return (new ProjectShowResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProjectShowResource($project->load(['tasks']));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->all());

        if ($request->status) {
            $project->setStatus($request->status);
        }

        if($request->attached_files != null)
        {
            (new MediaService)->updateMedias($request->attached_files, $request->medias_to_delete_ids, Project::ATTACHED_FILES_MEDIA_COLLECTION, $project);
        }

        return (new ProjectShowResource($project->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Project $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function statuses()
    {
        return Project::STATUSES;
    }
}