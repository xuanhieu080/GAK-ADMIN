<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class PostResource extends JsonResource
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
        $data = $this->resource->toArray();
        $data['image_url'] = $this->getFirstMediaUrl();
        $data['author_name'] = object_get($this, 'author.name');
        $data['group_name'] = object_get($this->resource, 'group.name');

        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
