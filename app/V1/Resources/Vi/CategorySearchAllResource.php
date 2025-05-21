<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategorySearchAllResource extends JsonResource
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
            'id'               => $this->id,
            'code'             => $this->code,
            'image'            => $image,
            'image_url'        => $image,
            'name'             => $this->name,
            'description'      => $this->description,
            'is_active'        => $this->is_active,
            'parent_id'        => $this->parent_id,
            'show_header'      => $this->show_header,
            'show_dashboard'   => $this->show_dashboard,
            'slug'             => $this->slug,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_key'         => $this->meta_key,
            'order'            => $this->order,
            'content_seo'      => $this->content_seo,
            'products'         => ProductVariantResource::collection($this->variants),
            'created_at'       => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at'       => $this->updated_at ? $this->updated_at->diffForHumans() : null,
        ];
    }
}
