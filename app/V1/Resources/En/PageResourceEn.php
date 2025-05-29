<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PageResourceEn extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name_en ?? $this->name,
            'title'             => $this->title_en ?? $this->title,
            'slug'              => $this->slug_en,
            'slug_other'        => $this->slug,
            'description'       => $this->description_en ?? $this->description,
            'description_short' => $this->description_short_en ?? $this->description_short,
            'image'             => $image,
            'image_url'         => $image,
            'user_id'           => $this->user_id,
            'group_id'          => $this->group_id,
            'is_active'         => $this->is_active,
            'show_header'       => $this->show_header,
            'meta_title'        => $this->meta_title_en ?? $this->meta_title,
            'meta_description'  => $this->meta_description_en ?? $this->meta_description,
            'meta_key'          => $this->meta_key_en ?? $this->meta_key,
            'is_button'         => $this->is_button,
            'link'              => $this->link_en,
            'created_at'        => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at'        => $this->updated_at ? $this->updated_at->diffForHumans() : null,
        ];
    }
}
