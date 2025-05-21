<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryChildrenResource extends JsonResource
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
        return [
            'code'       => $this->code,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'is_active'  => $this->is_active,
            'image_url'  => $this->getFirstMediaUrl()
        ];
    }
}
