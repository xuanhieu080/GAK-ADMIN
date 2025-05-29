<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class AttributeGroupShortResource extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->name,
            'title'    => $this->name,
            'slug'     => $this->slug,
            'priority' => $this->priority,
            'is_color' => $this->is_color,
            'is_main'  => $this->is_main,
        ];
    }
}
