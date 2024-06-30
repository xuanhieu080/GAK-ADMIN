<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantMainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $thumb = [];
        $data = [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $this->name,
            'product_id'              => $this->product_id,
            'product_name'            => object_get($this, 'product.name'),
            'slug'                    => object_get($this, 'product.slug'),
            'price'                   => $this->price,
            'qty'                     => $this->qty,
            'is_active'               => $this->is_active,
            'price_discount'          => $this->price_discount,
            'discount'                => $this->discount,
            'percent'                 => $this->price <= 0 ? 0 : (int)(round($this->discount / $this->price, 2) * 100),
            'options'                 => $this->options,
            'option_all'              => $this->option_all,
            'option_group'            => $this->option_group,
        ];

        $image = null;
        if (!empty($this->productVariantMain)) {
            foreach ($this->productVariantMain->getMedia("thumb") as $item) {
                $thumb[] = $item->getFullUrl();
            }
            $image = $this->productVariantMain->getFirstMediaUrl();
//            $data['image'] = $this->productVariantMain->getFirstMediaUrl();
        } else {
            $image = $this->product->getFirstMediaUrl();
//            $data['image'] = $this->product->getFirstMediaUrl();
            foreach ($this->product->getMedia("thumb") as $item) {
                $thumb[] = $item->getFullUrl();
            }
        }

        if (empty($thumb)) {
            foreach ($this->product->getMedia("thumb") as $item) {
                $thumb[] = $item->getFullUrl();
            }
        }
        $data['image_url'] = !empty($thumb[0]) ? $thumb[0] : $image;
        $data['image'] = !empty($thumb[0]) ? $thumb[0] : $image;
        $data['thumb_image'] = $thumb;
        $data['out_of_stock'] = empty($thumb) || empty($data['image']) || $this->qty <= 0;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
