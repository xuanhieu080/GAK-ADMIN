<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\AttributeResource;
use App\Http\Requests\Attributes\StoreRequest;
use App\Http\Requests\Attributes\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Product\AttributeService;

class AttributeController extends Controller
{
    /**
     * The service instance
     * @var AttributeService
     */
    private AttributeService $attributeService;

    /**
     * Constructor
     */
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService= $attributeService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Attribute::class);

        return $this->attributeService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Attribute::class);

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
//        $this->authorize('create', Attribute::class);

        $input = $request->validated();
        $record = $this->attributeService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Attribute  $attribute
     *
     * @return AttributeResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Attribute $attribute)
    {
//        $this->authorize('view', Attribute::class);

        $model = $this->attributeService->get($attribute);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Attribute  $attribute
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Attribute $attribute)
    {
//        $this->authorize('edit', Attribute::class);

        return $this->show($attribute);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Attribute  $attribute
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Attribute $attribute)
    {
//        $this->authorize('edit', Attribute::class);

        $data = $request->validated();
        if ($item = $this->attributeService->update($attribute, $data)) {
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
    public function destroy(DestroyUserRequest $request, Attribute $attribute)
    {
//        $this->authorize('delete', Attribute::class);

        if ($this->attributeService->delete($attribute)) {
            return $this->responseDeleteSuccess(['model' => $attribute]);
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
