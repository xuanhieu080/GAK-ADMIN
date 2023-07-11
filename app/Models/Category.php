<?php

namespace App\Models;

use App\Supports\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    const path = 'categories';

    protected $fillable = [
        'id',
        'code',
        'image',
        'name',
        'description',
        'is_active',
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return Storage::disk(HasImage::disk())->url($this->image);
        }
        return null;
    }
}
