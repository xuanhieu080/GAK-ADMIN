<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariantMain extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'id',
        'code',
        'product_id',
        'is_active',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_key',
        'options',
        'option_all',
        'option_group',
        'option_name',
        'params',
    ];

    protected $casts = [
        'options'      => 'array',
        'option_all'   => 'array',
        'option_group' => 'array',
        'is_active'    => 'boolean',
    ];

    public function getMediaFolderName()
    {
        return 'product-variant-mains';
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_main_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function variantItem()
    {
        return $this->hasOne(ProductVariant::class, 'product_main_id', 'id');

    }
}
