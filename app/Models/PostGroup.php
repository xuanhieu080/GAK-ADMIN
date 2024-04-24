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

class PostGroup extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'image',
        'name',
        'description',
        'is_active',
        'user_id',
        'slug',
        'meta_title',
        'meta_description',
        'meta_key',
        'is_hot',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_hot'    => 'boolean',
    ];

    protected $searchFields = ['name'];

    public function posts() {
        return $this->hasMany(Post::class, 'group_id', 'id');
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
        return 'post-groups';
    }
}
