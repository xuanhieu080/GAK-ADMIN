<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                  => $this->id,
            'code'                => $this->code,
            'image'               => $this->code,
            'name'                => $this->name,
            'name_en'             => $this->name_en,
            'description'         => $this->description,
            'description_en'      => $this->description_en,
            'is_active'           => $this->is_active,
            'parent_id'           => $this->parent_id,
            'show_header'         => $this->show_header,
            'show_dashboard'      => $this->show_dashboard,
            'slug'                => $this->slug,
            'slug_en'             => $this->slug_en,
            'meta_title'          => $this->meta_title,
            'meta_title_en'       => $this->meta_title_en,
            'meta_description'    => $this->meta_description,
            'meta_description_en' => $this->meta_description_en,
            'meta_key'            => $this->meta_key,
            'meta_key_en'         => $this->meta_key_en,
            'order'               => $this->order,
            'content_seo'         => $this->content_seo,
            'content_seo_en'      => $this->content_seo_en,
        ];
        $data['image_url'] = $this->getFirstMediaUrl();
        if (!empty($this->resource->toArray()['descendants'])) {
            $data['descendants'] = CategoryDescendantResource::collection($this->descendants);
        }
//        $data['variants'] = $this->getFirstMediaUrl();
//        if (!empty($this->resource->toArray()['products'])) {
//            $data['products'] = ProductResource::collection($this->products);
//        }

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
