<?php

namespace App\V1\Controllers;


use App\Models\Page;
use App\V1\Models\PageModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new PageModel();
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

    public function getPageHeader(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->getPageHeader($input);
    }



    public function show($slug)
    {
//        $this->authorize('view', Company::class);
        $model = $this->model->getItem($slug);
        if (empty($model)) {
            return $this->responseFail('Dữ liệu không tồn tại',[]);
        }
        return $model;
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
