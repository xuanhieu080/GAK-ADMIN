<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class WardResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'code'          => $this->code,
            'name_en'       => $this->name_en,
            'full_name'     => $this->full_name,
            'full_name_en'  => $this->full_name_en,
            'code_name'     => $this->code_name,
            'district_code' => $this->district_code,
            'district_id'   => $this->district_id,
        ];

        return $data;
    }
}
