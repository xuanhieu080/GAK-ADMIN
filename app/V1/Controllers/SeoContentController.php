<?php

namespace App\V1\Controllers;

use App\V1\Models\SeoContentModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoContentController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new SeoContentModel();
    }

    public function show($link)
    {
        $model = $this->model->show($link);
        return $this->responseDataSuccess(['data' => $model]);
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
