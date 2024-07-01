<?php

namespace App\V1\Models;

use App\Models\SeoContent;
use App\V1\Resources\SeoContentResource;

class SeoContentModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new SeoContent();
        parent::__construct($model);
    }


    public function show($link)
    {
        $item = SeoContent::query()->where('link', $link)->first();
        if (empty($item)) {
            return response()->json(null);
        }
        return new SeoContentResource($item);
    }
}
