<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CategoryDescendantResource extends JsonResource
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
//        $data = [
//            'id',
//            'code',
//            'image',
//            'name',
//            'name_en',
//            'description',
//            'description_en',
//            'is_active',
//            '_lft',
//            '_rgt',
//            'parent_id',
//            'show_header',
//            'show_dashboard',
//            'slug',
//            'slug_en',
//            'meta_title',
//            'meta_title_en',
//            'meta_description',
//            'meta_description_en',
//            'meta_key',
//            'meta_key_en',
//            'order',
//            'content_seo',
//            'content_seo_en'
//        ];
        $data = $this->resource->toArray();
        if (!empty($this->resource->toArray()['products'])) {
            $data['products'] = ProductShortResource::collection($this->products);
        }
        $data['image_url'] = $this->getFirstMediaUrl();

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
