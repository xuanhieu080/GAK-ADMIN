<?php

namespace App\V1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class SeoContentResource extends JsonResource
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
        $data = [
            'id'          => $this->id,
            'link'        => $this->link,
            'description' => $this->description,
            'is_active'   => $this->is_active,
        ];

        return $data;
    }
}
