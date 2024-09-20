<?php

namespace App\V1\Controllers;


use App\Models\Product;
use App\V1\Models\ProductModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProductModel();
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
    public function hot(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->hot($input);
    }
    public function new(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->new($input);
    }
    public function upcoming(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->upcoming($input);
    }
    public function uniform(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->uniform($input);
    }

    public function show($slug, Request $request)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($slug, $request->all());

        if (empty($model)) {
            return $this->responseFail('Dữ liệu không tồn tại',[]);
        }
        return $this->responseDataSuccess(['data' => $model, 'properties' => $this->properties()]);
    }

    public function getReview($slug, Request $request)
    {
//        $this->authorizeize('attribute', Product::class);

        $input = $request->all();
        $input['slug'] = $slug;
        return $this->model->getReview( $input);
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
