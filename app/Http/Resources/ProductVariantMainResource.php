<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantMainResource extends JsonResource
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
        $data['image'] = $this->getFirstMediaUrl();
        $data['price'] = object_get($this, 'variantItem.price', $this->product->price);
        $data['discount'] = object_get($this, 'variantItem.discount', $this->product->discount);
        $data['slug'] = $this->product->slug;
        $data['code'] = $this->product->code;
        $data['images'] = [];
        if ($this->getFirstMediaUrl()) {
            $data['images'] = [$this->getFirstMediaUrl()];
        }
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['percent'] = $data['price'] <= 0 ? 0 : round($data['discount'] / $data['price'], 2) * 100;
        $data['thumb_image'] = $thumb;
        $data['rel'] = "pondElement$this->id";
        $data['relThumb'] = "pondElementThumb$this->id";
        $data['variants'] = ProductVariantResource::collection($this->variants);

        return $data;
    }
}
