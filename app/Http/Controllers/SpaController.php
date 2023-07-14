<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Routing\Controller as BaseController;

class SpaController extends BaseController
{
    /**
     * Renders the main app screen
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function __invoke()
    {
        return view('index', ['config' => Config::pluck('description', 'code')]);
    }
}

