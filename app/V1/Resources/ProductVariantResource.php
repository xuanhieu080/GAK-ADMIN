<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $thumb = [];
        $data = [
            'id'             => $this->id,
            'code'           => $this->code,
            'product_id'     => $this->product_id,
            'product_name'   => object_get($this, 'product.name'),
            'slug'           => object_get($this, 'product.slug'),
            'description'    => $this->description,
            'price'          => $this->price,
            'category_id'    => object_get($this, 'product.category.id'),
            'qty'            => $this->coqtyde,
            'is_active'      => $this->is_active,
            'price_discount' => $this->price_discount,
            'discount'       => $this->discount,
        ];

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
