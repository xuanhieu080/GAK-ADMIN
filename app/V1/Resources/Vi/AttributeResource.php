<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class AttributeResource extends JsonResource
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
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'slug_other' => $this->slug_en,
            'color'      => $this->color,
            'is_color'   => filter_var(object_get($this, 'group.is_color'), FILTER_VALIDATE_BOOLEAN),
            'link'       => $this->link,
        ];
    }
}
