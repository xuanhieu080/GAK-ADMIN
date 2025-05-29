<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResourceEn extends JsonResource
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
            'id'               => $this->id,
            'code'             => $this->code,
            'slug'             => $this->slug_en,
            'slug_other'       => $this->slug,
            'name'             => $this->name_en ?? $this->name,
            //            'description'      => $this->description,
            'price'            => $this->price_en,
            'category_id'      => $this->category_id,
            'qty'              => $this->qty,
            'is_active'        => $this->is_active,
            'qty_sold'         => $this->qty_sold,
            'priority'         => $this->priority,
            'meta_title'       => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key'         => $this->meta_key_en ?? $this->meta_key,
            'video_link'       => $this->video_link,
            'price_discount'   => $this->price_discount_en,
            'discount'         => $this->discount_en,
            'percent'          => $this->price_en <= 0 ? 0 : (int)(round($this->discount_en / $this->price_en, 2) * 100),
            'is_hot'           => $this->is_hot,
            //            'variants'         => [],
        ];

//        if (!empty($this->resource->toArray()['variants'])) {
        $data['variants'] = ProductVariantResourceEn::collection($this->variants);
//        }

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
