<?php

namespace App\V1\Resources\En;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResourceEn extends JsonResource
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

        $productPrice = $product->price_en ?? 0;
        $productDiscount = $product->discount_en ?? 0;

        // Determine image source priority
        $imageSource = $productVariantMain ?? $product;

        // Get thumbnails efficiently
        $thumbUrls = $imageSource ? $this->getThumbUrls($imageSource) : [];
        $primaryImage = $imageSource?->getFirstMediaUrl();
        $finalImage = $thumbUrls[0] ?? $primaryImage;

        return [
            // Basic info
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $productVariantMain->name_en ?? $product->name_en ?? $product->name,
            'product_id'              => $this->product_id,
            'product_name'            => $product->name_en ?? $product->name,
            'qty'                     => $this->qty,
            'is_active'               => $this->is_active,

            // URLs
            'slug'                    => $product->slug_en ?? null,
            'slug_other'              => $product->slug ?? null,

            // Pricing
            'price'                   => $productPrice,
            'price_discount'          => $product->price_discount_en ?? null,
            'discount'                => $productDiscount,
            'percent'                 => $this->calculateDiscountPercent($productPrice, $productDiscount),

            // Category
            'category_id'             => $product->category?->id ?? null,

            // Variants and options
            'options'                 => $this->options,
            'option_all'              => $this->option_all,
            'option_group'            => $this->option_group,
            'product_variant_main_id' => $productVariantMain->id ?? null,

            // Images
            'image_url'               => $finalImage,
            'image'                   => $finalImage,
            'thumb_image'             => $thumbUrls,

            // Status
            'out_of_stock'            => $this->isOutOfStock($thumbUrls, $finalImage),

            // SEO
            'meta_title'              => $this->meta_title_en ?? $this->meta_title,
            'meta_description'        => $this->meta_description_en ?? $this->meta_description,
            'meta_key'                => $this->meta_key_en ?? $this->meta_key,

            // Timestamps
            'created_at'              => $this->created_at?->diffForHumans(),
            'updated_at'              => $this->updated_at?->diffForHumans(),
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
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $productVariantMain->name_en ?? null,
            'product_id'              => $this->product_id,
            'product_name'            => null,
            'qty'                     => $this->qty,
            'is_active'               => $this->is_active,
            'slug'                    => null,
            'slug_other'              => null,
            'price'                   => 0,
            'price_discount'          => null,
            'discount'                => 0,
            'percent'                 => 0,
            'category_id'             => null,
            'options'                 => $this->options,
            'option_all'              => $this->option_all,
            'option_group'            => $this->option_group,
            'product_variant_main_id' => $productVariantMain->id ?? null,
            'image_url'               => $finalImage,
            'image'                   => $finalImage,
            'thumb_image'             => $thumbUrls,
            'out_of_stock'            => true, // Always out of stock if no product
            'meta_title'              => $this->meta_title_en ?? $this->meta_title,
            'meta_description'        => $this->meta_description_en ?? $this->meta_description,
            'meta_key'                => $this->meta_key_en ?? $this->meta_key,
            'created_at'              => $this->created_at?->diffForHumans(),
            'updated_at'              => $this->updated_at?->diffForHumans(),
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

    public function toArraya($request)
    {
        $thumb = [];
        $data = [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => optional($this->productVariantMain)->name_en_en ?? optional($this->product)->name_en_en ?? optional($this->product)->name_en,
            'product_id'              => $this->product_id,
            'product_name'            => optional($this->product)->name_en_en ?? optional($this->product)->name_en,
            'slug'                    => object_get($this, 'product.slug_en'),
            'slug_other'              => object_get($this, 'product.slug'),
            //            'description'             => $this->description,
            'price'                   => $this->price_en,
            'category_id'             => object_get($this, 'product.category.id'),
            'qty'                     => $this->qty,
            'is_active'               => $this->is_active,
            'price_discount'          => $this->price_discount_en,
            'discount'                => $this->discount_en,
            'percent'                 => $this->price_en <= 0 ? 0 : (int)(round($this->discount_en / $this->price_en, 2) * 100),
            'options'                 => $this->options,
            'option_all'              => $this->option_all,
            'option_group'            => $this->option_group,
            'product_variant_main_id' => object_get($this, 'productVariantMain.id'),
            'meta_title'              => $this->meta_title_en ?? $this->meta_title,
            'meta_description'        => $this->meta_description_en ?? $this->meta_description,
            'meta_key'                => $this->meta_key_en ?? $this->meta_key,
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

//        if (empty($thumb)) {
//            foreach ($this->product->getMedia("thumb") as $item) {
//                $thumb[] = $item->getFullUrl();
//            }
//        }
        $data['image_url'] = !empty($thumb[0]) ? $thumb[0] : $image;
        $data['image'] = !empty($thumb[0]) ? $thumb[0] : $image;
        $data['thumb_image'] = $thumb;
        $data['out_of_stock'] = empty($thumb) || empty($data['image']) || $this->qty <= 0;
        $data['created_at'] = !empty($this->resource->created_at) ? $this->resource->created_at->diffForHumans() : null;
        $data['updated_at'] = !empty($this->resource->updated_at) ? $this->resource->updated_at->diffForHumans() : null;

        return $data;
    }
}
