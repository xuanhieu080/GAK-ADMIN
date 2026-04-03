<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantMainResourceEn extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        // Cache frequently accessed objects
        $product = $this->product;

        // Handle null product case early
        if (!$product) {
            return $this->getMinimalProductData();
        }

        // Cache product pricing data
        $productPriceEn = $product->price_en ?? 0;
        $productDiscountEn = $product->discount_en ?? 0;

        // Get thumbnails efficiently
        $thumbUrls = $this->getThumbUrls();
        $primaryImage = $this->getFirstMediaUrl();
        $finalImage = $thumbUrls[0] ?? $primaryImage;

        return [
            // Basic info with multilingual support
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name_en ?? $this->name,
            'product_id' => $this->product_id,
            'product_name' => $product->name_en ?? $product->name,
            'qty' => $this->qty,
            'is_active' => $this->is_active,

            // URLs with multilingual slugs
            'slug' => $product->slug_en,
            'slug_other' => $product->slug,

            // Pricing (EN version)
            'price' => $productPriceEn,
            'price_discount' => $product->price_discount_en,
            'discount' => $productDiscountEn,
            'percent' => $this->calculateDiscountPercent($productPriceEn, $productDiscountEn),

            // Variants and options
            'options' => $this->options,
            'option_all' => $this->option_all,
            'option_group' => $this->option_group,

            // Images
            'image_url' => $finalImage,
            'image' => $finalImage,
            'thumb_image' => $thumbUrls,

            // Status
            'out_of_stock' => $this->isOutOfStock($thumbUrls, $finalImage),

            // SEO with multilingual support
            'meta_title' => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key' => $this->meta_key_en ?? $this->meta_key,

            // Timestamps
            'created_at' => $this->resource?->created_at?->diffForHumans(),
            'updated_at' => $this->resource?->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Return minimal data when product is null
     */
    private function getMinimalProductData(): array
    {
        $thumbUrls = $this->getThumbUrls();
        $primaryImage = $this->getFirstMediaUrl();
        $finalImage = $thumbUrls[0] ?? $primaryImage;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name_en ?? $this->name,
            'product_id' => $this->product_id,
            'product_name' => null,
            'qty' => $this->qty,
            'is_active' => $this->is_active,
            'slug' => null,
            'slug_other' => null,
            'price' => 0,
            'price_discount' => null,
            'discount' => 0,
            'percent' => 0,
            'options' => $this->options,
            'option_all' => $this->option_all,
            'option_group' => $this->option_group,
            'image_url' => $finalImage,
            'image' => $finalImage,
            'thumb_image' => $thumbUrls,
            'out_of_stock' => true,
            'meta_title' => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key' => $this->meta_key_en ?? $this->meta_key,
            'created_at' => $this->resource?->created_at?->diffForHumans(),
            'updated_at' => $this->resource?->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Get thumbnail URLs with fallback to product images
     */
    private function getThumbUrls(): array
    {
        // Try to get thumbnails from current model first
        $media = $this->getMedia('thumb');
        $thumbUrls = $media
            ? $media->map(fn($item) => $item->getFullUrl())->toArray()
            : [];

        // Fallback to product thumbnails if empty
        if (empty($thumbUrls) && $this->product) {
            $productMedia = $this->product->getMedia('thumb');
            $thumbUrls = $productMedia
                ? $productMedia->map(fn($item) => $item->getFullUrl())->toArray()
                : [];
        }

        return $thumbUrls;
    }

    /**
     * Calculate discount percentage
     */
    private function calculateDiscountPercent(float $price, float $discount): int
    {
        return $price <= 0 ? 0 : (int)(round($discount / $price, 2) * 100);
    }

    /**
     * Check if product is out of stock
     */
    private function isOutOfStock(array $thumbUrls, ?string $image): bool
    {
        return empty($thumbUrls) || empty($image) || $this->qty <= 0;
    }
    public function toArraya($request)
    {
        $thumb = [];
        $data = [
            'id'               => $this->id,
            'code'             => $this->code,
            'name'             => $this->name_en ?? $this->name,
            'product_id'       => $this->product_id,
            'product_name'     => optional($this->product)->name_en ?? optional($this->product)->name,
            'slug'             => object_get($this, 'product.slug_en'),
            'slug_other'       => object_get($this, 'product.slug'),
            'price'            => object_get($this, 'product.price_en'),
            'qty'              => $this->qty,
            'is_active'        => $this->is_active,
            'price_discount'   => object_get($this, 'product.price_discount_en'),
            'discount'         => object_get($this, 'product.discount_en'),
            'percent'          => object_get($this, 'product.price_en') <= 0 ? 0 : (int)(round(object_get($this, 'product.discount_en') / object_get($this, 'product.price_en'), 2) * 100),
            'options'          => $this->options,
            'option_all'       => $this->option_all,
            'option_group'     => $this->option_group,
            'meta_title'       => $this->meta_title_en ?? $this->meta_title,
            'meta_description' => $this->meta_description_en ?? $this->meta_description,
            'meta_key'         => $this->meta_key_en ?? $this->meta_key,
        ];


        $image = $this->getFirstMediaUrl();
        foreach ($this->getMedia("thumb") as $item) {
            $thumb[] = $item->getFullUrl();
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
