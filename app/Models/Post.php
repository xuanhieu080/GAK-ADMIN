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

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    use Searchable, Filterable;

    const path = 'posts';

    protected $fillable = [
        'id',
        'title',
        'title_en',
        'slug',
        'slug_en',
        'content',
        'content_en',
        'image',
        'author_id',
        'comment_count',
        'is_active',
        'meta_title',
        'meta_title_en',
        'meta_description',
        'meta_description_en',
        'meta_key',
        'meta_key_en',
        'group_id',
        'is_hot',
        'is_new',
        'view',
        'created_at'
    ];

    protected $searchFields = ['title', 'slug'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_hot'    => 'boolean',
        'is_new'    => 'boolean',
        'view'      => 'int',
    ];

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function group()
    {
        return $this->hasOne(PostGroup::class, 'id', 'group_id');
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
        return 'posts';
    }
}
