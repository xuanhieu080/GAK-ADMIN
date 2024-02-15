<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    const path = 'products';

    protected $fillable = [
        'id',
        'code',
        'name',
        'image',
        'description',
        'price',
        'category_id',
        'qty',
        'is_active',
        'qty_sold',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'title',
    ];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return Storage::disk(HasImage::disk())->url($this->image);
        }
        return null;
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function category() {
        return $this->hasOne(Category::class,'id', 'category_id');
    }

    public function details() {
        return $this->hasMany(ProductDetail::class,'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }
}
