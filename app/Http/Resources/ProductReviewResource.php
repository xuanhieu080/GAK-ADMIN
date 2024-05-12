<?php

namespace App\Http\Resources;

use App\Models\ProductVariant;
use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data = $this->resource->toArray();
        $data['thumb'] = $thumb;
        $data['productVariant'] = new ProductVariantResource($this->productVariant);
        return $data;
    }
}
