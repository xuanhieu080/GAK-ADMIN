<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryDescendantResource extends JsonResource
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
        $image = $this->getFirstMediaUrl();
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'image'      => $image,
            'image_url'  => $image,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'slug_other' => $this->slug_en,
            'created_at' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->diffForHumans(): null,
        ];
    }
}
