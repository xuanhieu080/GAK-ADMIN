<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductShortResource extends JsonResource
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
            'code'             => $this->code,
            'slug'             => $this->slug_en,
            'name'             => $this->name_en,
            'price'            => $this->price_en,
            'category_id'      => $this->category_id,
            'qty'              => $this->qty,
            'is_active'        => $this->is_active,
            'price_discount'   => $this->price_discount_en,
            'discount'         => $this->discount_en,
            'is_hot'           => $this->is_hot,
        ];


        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['category_name'] = object_get($this, 'category.name_en');
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
