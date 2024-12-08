<?php

namespace App\V1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class TicketResource extends JsonResource
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
            'name'        => $this->name,
            'description' => $this->description,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'created_at'  => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
        ];

        return $data;
    }
}
