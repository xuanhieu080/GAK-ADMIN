<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResourceEn extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // Build attribute groups (colors / sizes / etc.)
        $variants = [];
        foreach (
            $this->attributeVariants
                ->sortByDesc('is_color')
                ->sortByDesc('is_hot')
                ->sortByDesc('is_main')
                ->groupBy('attribute_group_id') as $key => $item
        ) {
            $variants[] = [
                'id' => $key,
                'name' => $item[0]['attribute_group_name_en'] ?? $item[0]['attribute_group_name'],
                'slug' => $item[0]['attribute_group_slug_en'],
                'slug_other' => $item[0]['attribute_group_slug'],
                'link' => $item[0]['attribute_group_link_en'],
                'is_color' => $item[0]['is_color'],
                'attributes' => VariantResourceEn::collection($item),
            ];
        }

        // Build breadcrumb chain from category ancestors + self
        $categoryChain = [];
        $cat = $this->category; // belongsTo Category
        if ($cat) {
            // ancestors first (root -> parent -> ...)
            foreach ($cat->ancestors as $ancestor) {
                $categoryChain[] = [
                    'id' => $ancestor->id,
                    'name' => $ancestor->name_en ?? $ancestor->name,
                    'slug' => $ancestor->slug_en ?? $ancestor->slug,
                    'slug_other' => $ancestor->slug, // fallback VN slug
                ];
            }

            // then current category
            $categoryChain[] = [
                'id' => $cat->id,
                'name' => $cat->name_en ?? $cat->name,
                'slug' => $cat->slug_en ?? $cat->slug,
                'slug_other' => $cat->slug, // fallback VN slug
            ];
        }

        // top review: since we eager load reviews ordered by rate desc,
        // just pick first() instead of doing weird ->first() in with()
        $topReview = $this->reviews->first();

        // collect thumb images
        $thumb = [];
        foreach ($this->getMedia('thumb') as $m) {
            $thumb[] = $m->getFullUrl();
        }

        $data = [
            'id' => $this->id,
            'code' => $this->code,

            'slug' => $this->slug_en,
            'slug_other' => $this->slug,

            'name' => $this->name_en ?? $this->name,
            'description' => $this->description_en ?? $this->description,

            'price' => $this->price_en ?? 0,

            'category_id' => $this->category_id,

            // current category info (single node, for backward compatibility)
            'category' => $cat ? [
                'id' => $cat->id,
                'name' => $cat->name_en ?? $cat->name,
                'slug' => $cat->slug_en ?? $cat->slug,
                'slug_other' => $cat->slug, // vn slug fallback
            ] : null,

            // breadcrumb: all ancestors + self
            'categories' => $categoryChain,

            'qty' => $this->qty,
            'is_active' => $this->is_active,
            'qty_sold' => $this->qty_sold,
            'priority' => $this->priority,

            'meta_title' => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key' => $this->meta_key_en ?? $this->meta_key,

            'video_link' => $this->video_link,

            'price_discount' => $this->price_discount_en,
            'discount' => $this->discount_en,

            'percent' => ($this->price_en <= 0)
                ? 0
                : (int) (round($this->discount_en / $this->price_en, 2) * 100),

            'is_hot' => $this->is_hot,
            'rate' => $this->rate,
            'rate_count' => $this->rate_count == 0 ? 1 : $this->rate_count,
            'average_rate' => $this->rate_count == 0
                ? 0
                : round($this->rate / $this->rate_count, 1),

            'variants' => ProductVariantResourceEn::collection($this->variants),
            'variantMainDetail' => new ProductVariantMainResourceEn($this->variantMainDetail),
            'variantItem' => new ProductVariantMainResourceEn($this->variantItem),
            'variantAttribute' => (array) $variants,

            'highlight' => $this->highlight_en,
            'highlight_image_url' => $this->getFirstMediaUrl('highlight'),
            'highlight_image' => $this->getFirstMediaUrl('highlight'),

            'reviews' => $this->reviews,
            'top_review' => $topReview,

            'image' => $this->getFirstMediaUrl(),
            'image_url' => !empty($thumb[0]) ? $thumb[0] : $this->getFirstMediaUrl(),
            'thumb_image' => $thumb,

            // legacy for old FE
            'category_name' => data_get($this, 'category.name_en', data_get($this, 'category.name')),

            'created_at' => !empty($this->resource->created_at)
                ? $this->resource->created_at->diffForHumans()
                : null,
            'updated_at' => !empty($this->resource->updated_at)
                ? $this->resource->updated_at->diffForHumans()
                : null,
        ];

        return $data;
    }
}
