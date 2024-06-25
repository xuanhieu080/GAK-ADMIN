<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductReview extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const path = 'product-reviews';

    protected $fillable = [
        'id',
        'product_id',
        'product_variant_id',
        'product_variant_code',
        'product_code',
        'rate',
        'customer_name',
        'customer_id',
        'option_name',
        'option',
        'description',
        'reply',
        'user_id',
        'user_name',
        'date',
    ];


    public function productVariant()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_id');
    }


    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function getMediaFolderName()
    {
        return 'product-reviews';
    }
}
