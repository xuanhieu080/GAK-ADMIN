<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PostResourceEn extends JsonResource
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
            'id'               => $this->id,
            'slug'             => $this->slug_en,
            'slug_other'       => $this->slug,
            'title'            => $this->title_en ?? $this->title,
            'content'          => $this->content_en ?? $this->content,
            'author_id'        => $this->author_id,
            'author_name'      => object_get($this, 'author.name'),
            'meta_title'       => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key'         => $this->meta_key_en ?? $this->meta_key,
            'is_hot'           => $this->is_hot,
            'is_active'        => $this->is_active,
            'is_new'           => $this->is_new,
            'view'             => $this->view,
            'user_id'          => $this->user_id,
            'category_id'      => $this->group_id,
            'category_name'    => optional($this->group)->name_en ?? optional($this->group)->name,
            'category_slug'    => object_get($this, 'group.slug_en'),
        ];

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
