<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\SearchPostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostListResource;
use App\Http\Resources\Post\PostShowResource;
use App\Services\MediaService;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;
        $type = $request->type;

        $posts = Post::with(['post_category', 'secteur']);

        if($type != null)
        {
            $posts = $posts->where('type', $type);
        }
        return PostListResource::collection($posts->orderByDesc('created_at')->paginate($per_page));
    }

    public function search(SearchPostRequest $request)
    {
        $title = $request->title;
        $description = $request->description;
        $post_category_id = $request->post_category_id;
        $secteur_id = $request->secteur_id;
        $tags = $request->tags;
        $type = $request->type;
        $status = $request->status;
        $per_page = $request->per_page ?? 10;

        $posts = Post::with(['post_category', 'secteur'])->orderByDesc('created_at');

        if($title)
        {
            $posts = $posts->where('title', 'ILIKE', '%'.$title.'%')
                        ->orWhere('description', 'ILIKE', '%'.$title.'%')
                        ->orWhere('tags', 'ILIKE', '%'.$title.'%');
        }
        
        if($description)
        {
            $posts = $posts->where('description', 'ILIKE', '%'.$description.'%');
        }

        if($post_category_id)
        {
            $posts = $posts->where('post_category_id', $post_category_id);
        }

        if($type)
        {
            $posts = $posts->where('type', $type);
        }

        if($secteur_id)
        {   
            $posts = $posts->where('secteur_id', $secteur_id);
        }

        if($status)
        {
            $posts = $posts->currentStatus($status);
        }

        if($tags)
        {
            $posts = $posts->get()->filter(function($post) use ($tags){
                if($post->tags != null)
                {
                    foreach ($post->tags as $tag) {
                        foreach($tags as $request_tag)
                        {
                            if(str_contains($tag, $request_tag) || str_contains($request_tag, $tag));
                            {
                                return true;
                            }
                        }
                    }
                }
                return false;
            });
        }

        return PostListResource::collection($posts->paginate($per_page));
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->all());

        $post->setStatus($request->status ?? Post::STATUS_DRAFTED);

        if($request->cover != null)
        {
            $post->addMedia($request->cover)->toMediaCollection(Post::COVER_MEDIA_COLLECTION);
        }

        if($request->attached_files != null)
        {
            foreach($request->attached_files as $file)
            {
                if($file != null)
                {
                    $post->addMedia($file)->toMediaCollection(Post::ATTACHED_FILES_MEDIA_COLLECTION);
                }
            }
        }

        return (new PostShowResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Post $post)
    {
        // abort_if(Gate::denies('post_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostShowResource($post->load(['post_category', 'secteur']));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->all());

        if ($request->status != null) {
            $post->setStatus($request->status);
        }

        if($request->cover != null)
        {
            $post->media()->where('collection_name', Post::COVER_MEDIA_COLLECTION)->first()?->delete();
            $post->addMedia($request->cover)->toMediaCollection(Post::COVER_MEDIA_COLLECTION);
        }

        if($request->attached_files != null)
        {
            (new MediaService)->updateMedias($request->attached_files, $request->medias_to_delete_ids, Post::ATTACHED_FILES_MEDIA_COLLECTION, $post);
        }

        return (new PostShowResource($post->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Post $post)
    {
        abort_if(Gate::denies('post_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function statuses()
    {
        return Post::STATUSES;
    }

    public function types()
    {
        return Post::TYPES;
    }
}
