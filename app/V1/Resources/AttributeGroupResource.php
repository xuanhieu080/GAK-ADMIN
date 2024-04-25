<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class AttributeGroupResource extends JsonResource
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
            'name'       => $this->name,
            'slug'       => $this->slug,
            'attributes' => AttributeResource::collection($this->attributes),
        ];

        return $data;
    }
}
