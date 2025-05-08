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

class Page extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'name',
        'name_en',
        'title',
        'title_en',
        'slug',
        'slug_en',
        'description',
        'description_en',
        'description_short',
        'description_short_en',
        'image',
        'user_id',
        'group_id',
        'is_active',
        'show_header',
        'meta_title',
        'meta_title_en',
        'meta_description',
        'meta_description_en',
        'meta_key',
        'meta_key_en',
        'is_button',
        'link',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'show_header' => 'boolean',
        'is_button'   => 'boolean',
    ];

    protected $searchFields = ['name', 'title'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function getMediaFolderName()
    {
        return 'pages';
    }

    public function group() {
        return $this->hasOne(PageGroup::class, 'id', 'group_id');
    }
}
