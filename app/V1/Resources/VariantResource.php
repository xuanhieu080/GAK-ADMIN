<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class VariantResource extends JsonResource
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
        $thumb = [];
        $data = [
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'product_name'         => object_get($this, 'product.name'),
            'attribute_id'         => $this->attribute_id,
            'attribute_name'       => object_get($this, 'attribute.name'),
            'attribute_group_id'   => $this->attribute_id,
            'attribute_group_name' => object_get($this, 'attributeGroup.name'),
            'priority'             => $this->priority,
        ];

        return $data;
    }
}
