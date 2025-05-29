<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class AttributeGroupShortResourceEn extends JsonResource
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
            'name'       => $this->name_en ?? $this->name,
            'title'      => $this->name_en ?? $this->name,
            'slug'       => $this->slug_en,
            'slug_other' => $this->slug,
            'priority'   => $this->priority,
            'is_color'   => $this->is_color,
            'is_main'    => $this->is_main,
        ];
    }
}
