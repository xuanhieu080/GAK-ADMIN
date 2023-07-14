<?php

namespace App\Http\Resources;

use App\Supports\CMS;
use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class ConfigResource extends JsonResource
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
        $value = $this->value;
        if ($this->is_file) {
            $value = $this->image_url;
        }
        return [
            'id'      => $this->id,
            'code'    => $this->code,
            'is_file' => $this->is_file,
            'value'   => $value,
        ];
    }
}
