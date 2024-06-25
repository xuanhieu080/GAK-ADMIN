<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'code',
        'product_id',
        'is_active',
        'name',
        'price',
        'qty',
        'description',
        'meta_title',
        'meta_description',
        'meta_key',
        'options',
        'option_all',
        'option_group',
        'option_name',
        'price_discount',
        'params',
        'discount',
        'product_main_id',
    ];

    protected $casts = [
        'options'      => 'array',
        'option_all'   => 'array',
        'option_group' => 'array',
        'is_active'    => 'boolean',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function getMediaFolderName()
    {
        return 'product-variants';
    }

    public function productVariantMain() {
        return $this->hasOne(ProductVariantMain::class, 'id', 'product_main_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
