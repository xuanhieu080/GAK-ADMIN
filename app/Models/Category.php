<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'slug',
        'meta_title',
        'meta_description',
        'meta_key',
    ];

//    protected $appends = [
//        'image_url'
//    ];
//
//    public function getImageUrlAttribute()
//    {
//        return $this->getFirstMediaUrl();
//    }

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
