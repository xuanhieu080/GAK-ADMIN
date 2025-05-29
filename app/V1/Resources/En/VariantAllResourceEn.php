<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class VariantAllResourceEn extends JsonResource
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
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'attribute_group_id'   => $this->attribute_group_id,
            'priority'             => $this->priority,
            'is_active'            => $this->is_active,
            'attribute_id'         => $this->attribute_id,
            'is_hot'               => $this->is_hot,
            'is_main'              => $this->is_main,
            "attribute_group_name" => $this->attribute_group_name_en ?? $this->attribute_group_name,
            "attribute_group_slug" => $this->attribute_group_slug_en,
            "attribute_group_link" => $this->attribute_group_link_en,
            "attribute_name"       => $this->attribute_name_en ?? $this->attribute_name,
            "attribute_slug"       => $this->attribute_slug_en,
            "is_color"             => $this->is_color,
            "attribute_group"      => new AttributeGroupShortResourceEn($this->attributeGroup),
            "attribute"            => new AttributeResourceEn($this->attribute)
        ];

        return $data;
    }
}
