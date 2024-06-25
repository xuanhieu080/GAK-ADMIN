<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();
        $data['image_url'] = $this->getFirstMediaUrl();
        if (!empty($this->resource->toArray()['products'])) {
            $data['products'] = ProductResource::collection($this->products);
        }
        if (!empty($this->resource->toArray()['descendants'])) {
            $data['descendants'] = CategoryDescendantResource::collection($this->descendants);
        }

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
