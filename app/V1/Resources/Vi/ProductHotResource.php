<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductHotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $variants = [];
        foreach ($this->attributeVariants->sortByDesc('is_color')->sortByDesc('is_hot')->sortByDesc('is_main')->groupBy('attribute_group_id') as $key => $item) {
            $variants[] = [
                'id'         => $key,
                'name'       => $item[0]['attribute_group_name'],
                'slug'       => $item[0]['attribute_group_slug'],
                'slug_other' => $item[0]['attribute_group_slug_en'],
                'link'       => $item[0]['attribute_group_link'],
                'is_color'   => $item[0]['is_color'],
                'attributes' => VariantResource::collection($item)
            ];
        }

        $thumb = [];
        $data = [
            'id'                  => $this->id,
            'code'                => $this->code,
            'slug'                => $this->slug,
            'slug_other'          => $this->slug_en,
            'name'                => $this->name,
            'price'               => $this->price,
            'category_id'         => $this->category_id,
            'qty'                 => $this->qty,
            'is_active'           => $this->is_active,
            'qty_sold'            => $this->qty_sold,
            'priority'            => $this->priority,
            'price_discount'      => $this->price_discount,
            'discount'            => $this->discount,
            'percent'             => $this->price <= 0 ? 0 : (int)(round($this->discount / $this->price, 2) * 100),
            'is_hot'              => $this->is_hot,
            'rate'                => $this->rate,
            'rate_count'          => $this->rate_count == 0 ? 1 : $this->rate_count,
            'average_rate'        => $this->rate_count == 0 ? 0 : round($this->rate / $this->rate_count, 1),
        ];


        $data['variantAttribute'] = (array)$variants;

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = !empty($thumb[0]) ? $thumb[0] : $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
