<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\SupportResource;
use App\Http\Requests\Supports\StoreRequest;
use App\Http\Requests\Supports\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Support\SupportService;

class SupportController extends Controller
{
    /**
     * The service instance
     * @var SupportService
     */
    protected SupportService $supportService;

    /**
     * Constructor
     */
    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', Support::class);

        return $this->supportService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Support::class);

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
        $this->authorize('create', Support::class);

        $input = $request->validated();
        $record = $this->supportService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Support  $support
     *
     * @return SupportResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Support $support)
    {
        $this->authorize('view', Support::class);

        $model = $this->supportService->get($support);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Support  $support
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Support $support)
    {
        $this->authorize('edit', Support::class);

        return $this->show($support);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Support  $support
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Support $support)
    {
        $this->authorize('edit', Support::class);

        $data = $request->validated();
        if ($this->supportService->update($support, $data)) {
            return $this->responseUpdateSuccess(['model' => $support->fresh()]);
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
    public function destroy(DestroyUserRequest $request, Support $support)
    {
        $this->authorize('delete', Support::class);

        if ($this->supportService->delete($support)) {
            return $this->responseDeleteSuccess(['model' => $support]);
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
