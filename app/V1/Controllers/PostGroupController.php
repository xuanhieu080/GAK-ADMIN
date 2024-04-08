<?php

namespace App\V1\Controllers;


use App\Models\PostGroup;
use App\V1\Models\PostGroupModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostGroupController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new PostGroupModel();
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

    public function show(PostGroup $post_group)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($post_group);
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
