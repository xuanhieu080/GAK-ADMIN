<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();
        $data['product_id'] = $this->product_id;
        $data['product_detail_id'] = $this->product_detail_id;
        $data['product_name'] = object_get($this, 'product.name');
        $data['product_image'] = object_get($this, 'product.image_url');
        $data['product_price'] = object_get($this, 'product.price');
        $data['product_description'] = object_get($this, 'productDetail.description');
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
