<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class VariantShortResourceEn extends JsonResource
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
            'id'              => $this->id,
            'attribute_color' => object_get($this, 'attribute.color'),
        ];

        return $data;
    }
}
