<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Post\StorePostCategoryRequest;
use App\Http\Requests\Post\SearchPostCategoryRequest;
use App\Http\Requests\Post\UpdatePostCategoryRequest;
use App\Http\Resources\Post\PostCategoryListResource;
use App\Http\Resources\Post\PostCategoryShowResource;

class PostCategoryController extends Controller
{
    public function index(Request $request)
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;

        return new PostCategoryListResource(PostCategory::paginate($per_page));
    }

    public function search(SearchPostCategoryRequest $request)
    {
        $title = $request->title;
        $description = $request->description;
        $per_page = $request->per_page ?? 10;

        $post_categories = PostCategory::query()->orderByDesc('created_at');

        if($title)
        {
            $post_categories = $post_categories->where('title', 'ILIKE', '%'.$title.'%');
        }
        
        if($description)
        {
            $post_categories = $post_categories->where('description', 'ILIKE', '%'.$description.'%');
        }

        return new PostCategoryListResource($post_categories->paginate($per_page));
    }

    public function store(StorePostCategoryRequest $request)
    {
        $post_category = PostCategory::create($request->all());

        return (new PostCategoryShowResource($post_category))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PostCategory $post_category)
    {
        abort_if(Gate::denies('post_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostCategoryShowResource($post_category);
    }

    public function update(UpdatePostCategoryRequest $request, PostCategory $post_category)
    {
        $post_category->update($request->all());

        return (new PostCategoryShowResource($post_category))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PostCategory $post_category)
    {
        abort_if(Gate::denies('post_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post_category->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
