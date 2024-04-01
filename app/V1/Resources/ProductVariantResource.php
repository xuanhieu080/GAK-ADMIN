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

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data = $this->resource->toArray();
        $data['image'] = $this->getFirstMediaUrl();
        $data['images'] = [];
        if ($this->getFirstMediaUrl()) {
            $data['images'] = [$this->getFirstMediaUrl()];
        }
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['rel'] = "pondElement$this->id";
        $data['relThumb'] = "pondElementThumb$this->id";

        return $data;
    }
}
