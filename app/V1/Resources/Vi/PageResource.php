<?php

namespace App\V1\Resources\Vi;

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
            'name'              => $this->name,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'slug_other'        => $this->slug_en,
            'description'       => $this->description,
            'description_short' => $this->description_short,
            'image'             => $image,
            'image_url'         => $image,
            'user_id'           => $this->user_id,
            'group_id'          => $this->group_id,
            'is_active'         => $this->is_active,
            'show_header'       => $this->show_header,
            'meta_title'        => $this->meta_title,
            'meta_description'  => $this->meta_description,
            'meta_key'          => $this->meta_key,
            'is_button'         => $this->is_button,
            'link'              => $this->link,
            'created_at'        => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at'        => $this->updated_at ? $this->updated_at->diffForHumans() : null,
        ];
    }
}
