<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PageResource extends JsonResource
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
            'name'              => $this->name_en,
            'title'             => $this->title_en,
            'slug'              => $this->slug_en,
            'description'       => $this->description_en,
            'description_short' => $this->description_short_en,
            'image'             => $image,
            'image_url'         => $image,
            'user_id'           => $this->user_id,
            'group_id'          => $this->group_id,
            'is_active'         => $this->is_active,
            'show_header'       => $this->show_header,
            'meta_title'        => $this->meta_title_en,
            'meta_description'  => $this->meta_description_en,
            'meta_key'          => $this->meta_key_en,
            'is_button'         => $this->is_button,
            'link'              => $this->link_en,
            'created_at'        => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at'        => $this->updated_at ? $this->updated_at->diffForHumans() : null,
        ];
    }
}
