<?php

namespace App\Http\Controllers;

use App\Models\PageGroup;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PageGroupResource;
use App\Http\Requests\PageGroups\StoreRequest;
use App\Http\Requests\PageGroups\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Page\PageGroupService;

class PageGroupController extends Controller
{
    /**
     * The service instance
     * @var PageGroupService
     */
    private PageGroupService $pageGroupService;

    /**
     * Constructor
     */
    public function __construct(PageGroupService $pageGroupService)
    {
        $this->pageGroupService = $pageGroupService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', PageGroup::class);

        return $this->pageGroupService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', PageGroup::class);

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
        $this->authorize('create', PageGroup::class);

        $input = $request->validated();
        $record = $this->pageGroupService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  PageGroup  $pageGroup
     *
     * @return PageGroupResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(PageGroup $pageGroup)
    {
        $this->authorize('view', PageGroup::class);

        $model = $this->pageGroupService->get($pageGroup);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PageGroup  $pageGroup
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(PageGroup $pageGroup)
    {
        $this->authorize('edit', PageGroup::class);

        return $this->show($pageGroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  PageGroup  $pageGroup
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, PageGroup $pageGroup)
    {
        $this->authorize('edit', PageGroup::class);

        $data = $request->validated();
        if ($item = $this->pageGroupService->update($pageGroup, $data)) {
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
    public function destroy(DestroyUserRequest $request, PageGroup $pageGroup)
    {
        $this->authorize('delete', PageGroup::class);

        if ($this->pageGroupService->delete($pageGroup)) {
            return $this->responseDeleteSuccess(['model' => $pageGroup]);
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
