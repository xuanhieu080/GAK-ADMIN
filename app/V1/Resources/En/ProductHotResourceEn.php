<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductHotResourceEn extends JsonResource
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
                'slug'       => $item[0]['attribute_group_slug_en'],
                'slug_other' => $item[0]['attribute_group_slug'],
                'link'       => $item[0]['attribute_group_link_en'],
                'is_color'   => $item[0]['is_color'],
                'attributes' => VariantShortResourceEn::collection($item)
            ];
        }

        $thumb = [];
        $data = [

            'id'                  => $this->id,
            'code'                => $this->code,
            'slug'                => $this->slug_en,
            'slug_other'          => $this->slug,
            'name'                => $this->name_en ?? $this->name,
            'price'               => $this->price_en,
            'qty'                 => $this->qty,
            'is_active'           => $this->is_active,
            'price_discount'      => $this->price_discount_en,
            'discount'            => $this->discount_en,
            'percent'             => $this->price_en <= 0 ? 0 : (int)(round($this->discount_en / $this->price_en, 2) * 100),
        ];


        $data['variantAttribute'] = (array)$variants;


        $image =  $this->getFirstMediaUrl();
        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
        }
        $data['image'] = $image;
        $data['image_url'] = !empty($thumb[0]) ? $thumb[0] : $image;
        $data['thumb_image'] = $thumb;

        return $data;
    }
}
