<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PageShortResource extends JsonResource
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
            'group_id'          => $this->group_id,
            'is_active'         => $this->is_active,
            'show_header'       => $this->show_header,
            'is_button'         => $this->is_button,
            'link'              => $this->link,
        ];
    }
}
