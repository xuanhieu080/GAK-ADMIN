<?php

namespace App\V1\Controllers;


use App\Models\Variant;
use App\V1\Models\VariantModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new VariantModel();
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

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function searchGroup(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->_index($input);
    }

    public function show(Variant $variant)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($variant);
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
