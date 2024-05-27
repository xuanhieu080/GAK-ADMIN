<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class OrderResource extends JsonResource
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
        $data = $this->resource->toArray();
        $data['details'] = OrderDetailResource::collection($this->details);
        $data['customer_code'] = object_get($this, 'customer.code');
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;
        $data['time'] = date('m-d-Y H:i:s', strtotime($this->date));
//        $data['details'] = $this->details;

        return $data;
    }
}
