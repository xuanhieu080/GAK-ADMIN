<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PostGroupResource extends JsonResource
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
            'slug'             => $this->slug,
            'name'             => $this->name,
            'description'      => $this->description,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_key'         => $this->meta_key,
            'is_hot'           => $this->is_hot,
            'is_active'        => $this->is_active,
            'user_id'          => $this->user_id
        ];
        if (!empty($this->resource->toArray()['posts'])) {
            $data['posts'] = PostResource::collection($this->posts);
        }
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
