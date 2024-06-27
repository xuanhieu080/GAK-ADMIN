<?php

namespace App\Http\Controllers;

use App\Models\PostGroup;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostGroupResource;
use App\Http\Requests\PostGroups\StoreRequest;
use App\Http\Requests\PostGroups\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Post\PostGroupService;

class PostGroupController extends Controller
{
    /**
     * The service instance
     * @var PostGroupService
     */
    private PostGroupService $postGroupService;

    /**
     * Constructor
     */
    public function __construct(PostGroupService $postGroupService)
    {
        $this->postGroupService = $postGroupService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', PostGroup::class);

        return $this->postGroupService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', PostGroup::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
//        $this->authorize('create', PostGroup::class);

        $input = $request->validated();
        $record = $this->postGroupService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param PostGroup $post_group
     *
     * @return PostGroupResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(PostGroup $post_group)
    {
//        $this->authorize('view', PostGroup::class);

        $model = $this->postGroupService->get( $post_group);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PostGroup $post_group
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(PostGroup $post_group)
    {
//        $this->authorize('edit', PostGroup::class);

        return $this->show( $post_group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param PostGroup $post_group
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, PostGroup $post_group)
    {
//        $this->authorize('edit', PostGroup::class);

        $data = $request->validated();
        if ($item = $this->postGroupService->update( $post_group, $data)) {
            return $this->responseUpdateSuccess(['model' => $item]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, PostGroup $post_group)
    {
//        $this->authorize('delete', PostGroup::class);

        if ($this->postGroupService->delete( $post_group)) {
            return $this->responseDeleteSuccess(['model' =>  $post_group]);
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
