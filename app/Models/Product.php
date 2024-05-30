<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    use Searchable, Filterable;

    const path = 'products';

    protected $fillable = [
        'id',
        'code',
        'slug',
        'name',
        'description',
        'price',
        'category_id',
        'qty',
        'is_active',
        'qty_sold',
        'priority',
        'meta_title',
        'meta_description',
        'meta_key',
        'video_link',
        'price_discount',
        'discount',
        'is_hot',
        'is_new',
        'is_upcoming',
        'is_uniform',
        'rate',
        'rate_count',
        'highlight_image',
        'highlight'
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_hot'      => 'boolean',
        'is_uniform'  => 'boolean',
        'is_new'      => 'boolean',
        'is_upcoming' => 'boolean',
    ];

    protected $searchFields = ['name'];

    protected $appends = [
        'title',
    ];

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class, 'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function variantMains()
    {
        return $this->hasMany(ProductVariantMain::class, 'product_id', 'id');
    }

    public function attributeVariants()
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id');
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
        return 'products';
    }
}
