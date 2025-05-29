<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResourceEn extends JsonResource
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
                'name'       => $item[0]['attribute_group_name_en'] ?? $item[0]['attribute_group_name'],
                'slug'       => $item[0]['attribute_group_slug_en'] ?? $item[0]['attribute_group_slug'],
                'link'       => $item[0]['attribute_group_link_en'] ?? $item[0]['attribute_group_link'],
                'is_color'   => $item[0]['is_color'],
                'attributes' => VariantResourceEn::collection($item)
            ];
        }

        $thumb = [];
        $data = [
            'id'                  => $this->id,
            'code'                => $this->code,
            'slug'                => $this->slug_en,
            'name'                => $this->name_en ?? $this->name,
            //            'description'         => $this->description,
            'price'               => $this->price_en,
            'category_id'         => $this->category_id,
            'qty'                 => $this->qty,
            'is_active'           => $this->is_active,
            'qty_sold'            => $this->qty_sold,
            'priority'            => $this->priority,
            'meta_title'          => $this->meta_title_en ?? $this->meta_title,
            'meta_description'    => $this->meta_description_en ?? $this->meta_description,
            'meta_key'            => $this->meta_key_en ?? $this->meta_key,
            'video_link'          => $this->video_link,
            'price_discount'      => $this->price_discount_en,
            'discount'            => $this->discount_en,
            'percent'             => $this->price_en <= 0 ? 0 : (int)(round($this->discount_en / $this->price_en, 2) * 100),
            'is_hot'              => $this->is_hot,
            'rate'                => $this->rate,
            'rate_count'          => $this->rate_count == 0 ? 1 : $this->rate_count,
            'average_rate'        => $this->rate_count == 0 ? 0 : round($this->rate / $this->rate_count, 1),
            'highlight'           => $this->highlight_en,
            'highlight_image_url' => $this->getFirstMediaUrl('highlight'),
            'highlight_image'     => $this->getFirstMediaUrl('highlight'),
        ];

//        $productVariants = [];
//
//        foreach ($this->variants as $item) {
//            if (!empty($item->productVariantMain)) {
//                if (!empty($item->productVariantMain->getFirstMediaUrl() && count($item->productVariantMain->getMedia("thumb")) > 0)) {
//                    $productVariants[] = new ProductVariantResource($item);
//                }
//            }
//        }
//
////        if (!empty($this->resource->toArray()['variants'])) {
//        $data['variants'] = $productVariants;
        $data['variantAttribute'] = (array)$variants;
//        }

        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $this->getFirstMediaUrl();
        $data['image_url'] = !empty($thumb[0]) ? $thumb[0] : $this->getFirstMediaUrl();
        $data['thumb_image'] = $thumb;
        $data['category_name'] = object_get($this, 'category.name_en');
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
