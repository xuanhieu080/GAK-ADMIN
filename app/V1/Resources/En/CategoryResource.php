<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryResource extends JsonResource
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
        $data = [
            'id'               => $this->id,
            'code'             => $this->code,
            'image'            => $image,
            'image_url'        => $image,
            'name'             => $this->name_en,
            'description'      => $this->description_en,
            'is_active'        => $this->is_active,
            'parent_id'        => $this->parent_id,
            'show_header'      => $this->show_header,
            'show_dashboard'   => $this->show_dashboard,
            'slug'             => $this->slug_en,
            'meta_title'       => $this->meta_title_en,
            'meta_description' => $this->meta_description_en,
            'meta_key'         => $this->meta_key_en,
            'order'            => $this->order,
            'content_seo'      => $this->content_seo_en,
            'created_at'       => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at'       => $this->updated_at ? $this->updated_at->diffForHumans() : null,
        ];

        if (!empty($this->resource->toArray()['products'])) {
            $data['products'] = ProductResource::collection($this->products);
        }
        if (!empty($this->resource->toArray()['descendants'])) {
            $data['descendants'] = CategoryDescendantResource::collection($this->descendants);
        }

        return $data;
    }
}
