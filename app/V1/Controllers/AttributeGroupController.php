<?php

namespace App\V1\Controllers;


use App\Models\AttributeGroup;
use App\V1\Models\AttributeGroupModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeGroupController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new AttributeGroupModel();
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->index($input);
    }

    public function show(AttributeGroup $attribute_group, Request $request)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($attribute_group, $request->all());
        if (empty($model)) {
            return $this->responseFail('Dữ liệu không tồn tại',[]);
        }
        return $this->responseDataSuccess(['data' => $model, 'properties' => $this->properties()]);
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
