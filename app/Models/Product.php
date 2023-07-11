<?php

namespace App\Models;

use App\Supports\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

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

    /**
     * @return void
     */
    public function category() {
        return $this->hasOne(Category::class,'id', 'category_id');
    }

    /**
     * @return void
     */
    public function details() {
        return $this->hasMany(ProductDetail::class,'product_id', 'id');
    }
}
