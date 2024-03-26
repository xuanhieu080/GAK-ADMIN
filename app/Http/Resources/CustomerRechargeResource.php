<?php

namespace App\Http\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources
 */
class CustomerRechargeResource extends JsonResource
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

        $data['bank_name'] = object_get($this, 'bank.name');
        $data['bank_short_name'] = object_get($this, 'bank.short_name');
        $data['bank_code'] = object_get($this, 'bank.code');
        $data['customer_name'] = object_get($this, 'customer.name');
        $data['customer_code'] = object_get($this, 'customer.code');
        $data['user_name'] = object_get($this, 'user.name', 'Nạp qua ngân hàng');
        $data['status'] = $this->status;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
