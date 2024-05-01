<?php

namespace App\V1\Resources;

use App\Utilities\Data;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'name'                    => object_get($this,'productVariantMain.name'),
            'product_id'              => $this->product_id,
            'product_name'            => object_get($this, 'product.name'),
            'slug'                    => object_get($this, 'product.slug'),
            'description'             => $this->description,
            'price'                   => $this->price,
            'category_id'             => object_get($this, 'product.category.id'),
            'qty'                     => $this->qty,
            'is_active'               => $this->is_active,
            'price_discount'          => $this->price_discount,
            'discount'                => $this->discount,
            'options'                 => $this->options,
            'option_all'              => $this->option_all,
            'option_group'            => $this->option_group,
            'product_variant_main_id' => object_get($this, 'productVariantMain.id'),
        ];

        if (!empty($this->productVariantMain)) {
            foreach ($this->productVariantMain->getMedia("thumb") as $item) {
                $thumb[] = $item->getFullUrl();
            }
            $data['image'] = $this->productVariantMain->getFirstMediaUrl() ?: $this->product->getFirstMediaUrl();
            $data['image_url'] = $this->productVariantMain->getFirstMediaUrl() ?: $this->product->getFirstMediaUrl();
        } else {
            $data['image'] = $this->product->getFirstMediaUrl();
            $data['image_url'] = $this->product->getFirstMediaUrl();
        }

        if (empty($thumb)) {
            foreach ($this->product->getMedia("thumb") as $item) {
                $thumb[] = $item->getFullUrl();
            }
        }
        $data['thumb_image'] = $thumb;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
