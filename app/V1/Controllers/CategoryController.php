<?php

namespace App\V1\Controllers;


use App\Models\Category;
use App\V1\Models\CategoryModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new CategoryModel();
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

    public function getCategoryHeader(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->getCategoryHeader($input);
    }

    public function getCategoryDashboard(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->getCategoryDashboard($input);
    }



    public function show(Category $category)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($category);
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
