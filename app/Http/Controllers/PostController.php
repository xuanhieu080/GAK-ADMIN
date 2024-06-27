<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Post\PostService;

class PostController extends Controller
{
    /**
     * The service instance
     * @var PostService
     */
    protected PostService $postService;

    /**
     * Constructor
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Post::class);

        return $this->postService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Post::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
//        $this->authorize('create', Post::class);

        $input = $request->validated();
        $record = $this->postService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Post  $post
     *
     * @return PostResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Post $post)
    {
//        $this->authorize('view', Post::class);

        $model = $this->postService->get($post);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post  $post
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Post $post)
    {
//        $this->authorize('edit', Post::class);

        return $this->show($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Post  $post
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Post $post)
    {
//        $this->authorize('edit', Post::class);

        $data = $request->validated();
        if ($item = $this->postService->updateItem($post, $data)) {
            return $this->responseUpdateSuccess(['model' => $item]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, Post $post)
    {
//        $this->authorize('delete', Post::class);

        if ($this->postService->delete($post)) {
            return $this->responseDeleteSuccess(['model' => $post]);
        }

        return $this->responseDeleteFail();

    }

    /**
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }
}
