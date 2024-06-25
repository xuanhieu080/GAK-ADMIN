<?php

namespace App\V1\Controllers;


use App\V1\Models\PageGroupModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageGroupController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new PageGroupModel();
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
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }
}
