<?php

namespace App\V1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategorySearchAllResource extends JsonResource
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
        $data = [
            'id'         => $this->id,
            'code'         => $this->code,
            'name'         => $this->name,
            'slug'         => $this->slug,
        ];
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['products'] = ProductVariantResource::collection($this->variants);

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
