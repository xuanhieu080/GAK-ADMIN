<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Support extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    const path = 'supports';

    protected $fillable = [
        'id',
        'name',
        'description',
        'phone',
        'zalo',
        'telegram',
        'image',
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
