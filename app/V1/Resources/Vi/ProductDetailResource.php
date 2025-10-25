<?php

namespace App\V1\Resources\Vi;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // build variantAttribute y như bạn đang làm...
        $variants = [];
        foreach (
            $this->attributeVariants
                ->sortByDesc('is_color')
                ->sortByDesc('is_hot')
                ->sortByDesc('is_main')
                ->groupBy('attribute_group_id') as $key => $item
        ) {
            $variants[] = [
                'id'         => $key,
                'name'       => $item[0]['attribute_group_name'],
                'slug'       => $item[0]['attribute_group_slug'],
                'slug_other' => $item[0]['attribute_group_slug_en'],
                'link'       => $item[0]['attribute_group_link'],
                'is_color'   => $item[0]['is_color'],
                'attributes' => VariantResource::collection($item),
            ];
        }

        // thumbnails
        $thumb = [];
        foreach ($this->getMedia('thumb') as $m) {
            $thumb[] = $m->getFullUrl();
        }

        // --- BREADCRUMB CATEGORY BUILD ---
        $categoryChain = [];

        $cat = $this->category; // có thể null nếu product chưa gán category
        if ($cat) {
            // ancestors trước
            foreach ($cat->ancestors as $ancestor) {
                $categoryChain[] = [
                    'id'         => $ancestor->id,
                    'name'       => $ancestor->name,
                    'slug'       => $ancestor->slug,
                    'slug_other' => $ancestor->slug_en,
                ];
            }

            // cuối cùng là category hiện tại
            $categoryChain[] = [
                'id'         => $cat->id,
                'name'       => $cat->name,
                'slug'       => $cat->slug,
                'slug_other' => $cat->slug_en,
            ];
        }

        // --- TOP REVIEW (vì eager load reviews sort desc rate) ---
        $topReview = $this->reviews->first();

        $data = [
            'id'                  => $this->id,
            'code'                => $this->code,
            'slug'                => $this->slug,
            'slug_other'          => $this->slug_en,
            'name'                => $this->name,
            'description'         => $this->description,
            'price'               => $this->price,

            'category_id'         => $this->category_id,

            // thông tin category hiện tại (để FE vẫn dùng như cũ)
            'category' => $cat ? [
                'id'         => $cat->id,
                'name'       => $cat->name,
                'slug'       => $cat->slug,
                'slug_other' => $cat->slug_en,
            ] : null,

            // breadcrumb đầy đủ: root -> ... -> current
            'categories'          => $categoryChain,

            'qty'                 => $this->qty,
            'is_active'           => $this->is_active,
            'qty_sold'            => $this->qty_sold,
            'priority'            => $this->priority,
            'meta_title'          => $this->meta_title,
            'meta_description'    => $this->meta_description,
            'meta_key'            => $this->meta_key,
            'video_link'          => $this->video_link,
            'price_discount'      => $this->price_discount,
            'discount'            => $this->discount,
            'percent'             => $this->price <= 0
                ? 0
                : (int)(round($this->discount / $this->price, 2) * 100),

            'is_hot'              => $this->is_hot,
            'rate'                => $this->rate,
            'rate_count'          => $this->rate_count == 0 ? 1 : $this->rate_count,
            'average_rate'        => $this->rate_count == 0
                ? 0
                : round($this->rate / $this->rate_count, 1),

            'variants'            => ProductVariantResource::collection($this->variants),
            'variantMainDetail'   => new ProductVariantMainResource($this->variantMainDetail),
            'variantAttribute'    => (array)$variants,

            'highlight'           => $this->highlight,
            'highlight_image_url' => $this->getFirstMediaUrl('highlight'),
            'highlight_image'     => $this->getFirstMediaUrl('highlight'),

            'reviews'             => $this->reviews,
            'top_review'          => $topReview,

            'image'               => $this->getFirstMediaUrl(),
            'image_url'           => !empty($thumb[0]) ? $thumb[0] : $this->getFirstMediaUrl(),
            'thumb_image'         => $thumb,

            // giữ trường cũ để không gãy FE legacy
            'category_name'       => data_get($this, 'category.name'),

            'created_at'          => !empty($this->resource->created_at)
                ? $this->resource->created_at->diffForHumans()
                : null,
            'updated_at'          => !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans()
                : null,
        ];

        return $data;
    }

}
