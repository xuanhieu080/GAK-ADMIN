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
            'attribute_slug'       => $this->attribute_slug,
            'attribute_name'       => object_get($this, 'attribute.name'),
            'attribute_is_color'   => filter_var(object_get($this, 'attributeGroup.is_color'), FILTER_VALIDATE_BOOLEAN),
            'attribute_color'      => object_get($this, 'attribute.color'),
            'attribute_group_id'   => $this->attribute_group_id,
            'attribute_group_slug' => $this->attribute_group_slug,
            'attribute_group_name' => object_get($this, 'attributeGroup.name'),
            'priority'             => $this->priority,
            'is_hot'               => $this->is_hot,
            'is_main'              => $this->is_main,
        ];

        return $data;
    }
}
