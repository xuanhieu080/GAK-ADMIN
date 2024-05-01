<?php

namespace App\Http\Resources;

use App\Models\ProductVariantMain;
use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailMainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $thumb = [];

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data = $this->resource->toArray();
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['variantMains'] = ProductVariantMainResource::collection($this->variantMains);
        $data['category_name'] = object_get($this, 'category.name');
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
