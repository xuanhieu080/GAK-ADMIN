<?php

namespace App\V1\Resources\Vi;

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
        // Cache frequently accessed objects
        $product = $this->product;
        $productVariantMain = $this->productVariantMain;

        // Handle null product case
        if (!$product) {
            return $this->getMinimalProductData();
        }

        $productPrice = $product->price ?? 0;
        $productDiscount = $product->discount ?? 0;

        // Determine image source priority
        $imageSource = $productVariantMain ?? $product;

        // Get thumbnails efficiently
        $thumbUrls = $imageSource ? $this->getThumbUrls($imageSource) : [];
        $primaryImage = $imageSource?->getFirstMediaUrl();
        $finalImage = $thumbUrls[0] ?? $primaryImage;

        return [
            // Basic info
            'id' => $this->id,
            'code' => $this->code,
            'name' => $productVariantMain->name ?? $product->name,
            'product_id' => $this->product_id,
            'product_name' => $product->name,
            'qty' => $this->qty,
            'is_active' => $this->is_active,

            // URLs
            'slug' => $product->slug ?? null,
            'slug_other' => $product->slug_en ?? null,

            // Pricing
            'price' => $productPrice,
            'price_discount' => $product->price_discount ?? null,
            'discount' => $productDiscount,
            'percent' => $this->calculateDiscountPercent($productPrice, $productDiscount),

            // Category
            'category_id' => $product->category?->id ?? null,

            // Variants and options
            'options' => $this->options,
            'option_all' => $this->option_all,
            'option_group' => $this->option_group,
            'product_variant_main_id' => $productVariantMain->id ?? null,

            // Images
            'image_url' => $finalImage,
            'image' => $finalImage,
            'thumb_image' => $thumbUrls,

            // Status
            'out_of_stock' => $this->isOutOfStock($thumbUrls, $finalImage),

            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_key' => $this->meta_key,

            // Timestamps
            'created_at' => $this->created_at?->diffForHumans(),
            'updated_at' => $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Return minimal data when product is null
     */
    private function getMinimalProductData(): array
    {
        $productVariantMain = $this->productVariantMain;
        $thumbUrls = $productVariantMain ? $this->getThumbUrls($productVariantMain) : [];
        $primaryImage = $productVariantMain?->getFirstMediaUrl();
        $finalImage = $thumbUrls[0] ?? $primaryImage;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $productVariantMain->name ?? null,
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
            'category_id' => null,
            'options' => $this->options,
            'option_all' => $this->option_all,
            'option_group' => $this->option_group,
            'product_variant_main_id' => $productVariantMain->id ?? null,
            'image_url' => $finalImage,
            'image' => $finalImage,
            'thumb_image' => $thumbUrls,
            'out_of_stock' => true, // Always out of stock if no product
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_key' => $this->meta_key,
            'created_at' => $this->created_at?->diffForHumans(),
            'updated_at' => $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Get thumbnail URLs from media collection
     */
    private function getThumbUrls($imageSource): array
    {
        if (!$imageSource) {
            return [];
        }

        return $imageSource->getMedia('thumb')
            ->map(fn($item) => $item->getFullUrl())
            ->toArray();
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
}
