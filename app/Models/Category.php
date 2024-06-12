<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, NodeTrait, InteractsWithMedia;

    use Searchable, Filterable;

    const path = 'categories';

    protected $fillable = [
        'id',
        'code',
        'image',
        'name',
        'description',
        'is_active',
        '_lft',
        '_rgt',
        'parent_id',
        'show_header',
        'show_dashboard',
        'slug',
        'meta_title',
        'meta_description',
        'meta_key',
        'order',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'show_header'    => 'boolean',
        'show_dashboard' => 'boolean',
    ];
    protected $searchFields = ['name'];

//    protected $appends = [
//        'image_url'
//    ];
//
//    public function getImageUrlAttribute()
//    {
//        return $this->getFirstMediaUrl();
//    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function variantMains(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductVariantMain::class,
            Product::class,
            'category_id', // Foreign key on the environments table...
            'product_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }

    public function variants(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            Product::class,
            'category_id', // Foreign key on the environments table...
            'product_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }

    public function upTree()
    {
        return $this->newQuery()->where('_lft', '<', $this->_lft)->where('_rgt', '>', $this->_rgt);
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
        return 'categories';
    }

}
