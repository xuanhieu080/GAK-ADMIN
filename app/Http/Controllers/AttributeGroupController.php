<?php

namespace App\Http\Controllers;

use App\Models\AttributeGroup;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeGroupResource;
use App\Http\Requests\AttributeGroups\StoreRequest;
use App\Http\Requests\AttributeGroups\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Product\AttributeGroupService;

class AttributeGroupController extends Controller
{
    /**
     * The service instance
     * @var AttributeGroupService
     */
    private AttributeGroupService $attributeGroupService;

    /**
     * Constructor
     */
    public function __construct(AttributeGroupService $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', AttributeGroup::class);

        return $this->attributeGroupService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', AttributeGroup::class);

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
//        $this->authorize('create', AttributeGroup::class);

        $input = $request->validated();
        $record = $this->attributeGroupService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  AttributeGroup  $attributeGroup
     *
     * @return AttributeGroupResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(AttributeGroup $attributeGroup)
    {
//        $this->authorize('view', AttributeGroup::class);

        $model = $this->attributeGroupService->get($attributeGroup);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  AttributeGroup  $attributeGroup
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(AttributeGroup $attributeGroup)
    {
//        $this->authorize('edit', AttributeGroup::class);

        return $this->show($attributeGroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  AttributeGroup  $attributeGroup
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, AttributeGroup $attributeGroup)
    {
//        $this->authorize('edit', AttributeGroup::class);
        $data = $request->validated();
        if ($item = $this->attributeGroupService->update($attributeGroup, $data)) {
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
    public function destroy(DestroyUserRequest $request, AttributeGroup $attributeGroup)
    {
//        $this->authorize('delete', AttributeGroup::class);

        if ($this->attributeGroupService->delete($attributeGroup)) {
            return $this->responseDeleteSuccess(['model' => $attributeGroup]);
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
