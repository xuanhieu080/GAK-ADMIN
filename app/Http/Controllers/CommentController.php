<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Post\CommentService;

class CommentController extends Controller
{
    /**
     * The service instance
     * @var CommentService
     */
    protected CommentService $commentService;

    /**
     * Constructor
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', Comment::class);

        return $this->commentService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Comment::class);

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
/*    public function store(StoreRequest $request)
    {
        $this->authorize('create', Comment::class);

        $input = $request->validated();
        $record = $this->commentService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }*/

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Comment  $comment
     *
     * @return CommentResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Comment $comment)
    {
        $this->authorize('view', Comment::class);

        $model = $this->commentService->get($comment);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Comment  $comment
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Comment $comment)
    {
        $this->authorize('edit', Comment::class);

        return $this->show($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Comment  $comment
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
   /* public function update(UpdateRequest $request, Comment $comment)
    {
        $this->authorize('edit', Comment::class);

        $data = $request->validated();
        if ($this->commentService->update($comment, $data)) {
            return $this->responseUpdateSuccess(['model' => $comment->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, Comment $comment)
    {
        $this->authorize('delete', Comment::class);

        if ($this->commentService->delete($comment)) {
            return $this->responseDeleteSuccess(['model' => $comment]);
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
