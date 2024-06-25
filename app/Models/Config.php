<?php

namespace App\Models;

use App\Supports\HasImage;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Config extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    const path = 'configs';

    protected $table = 'configs';

    protected $fillable = [
        'id',
        'code',
        'value',
        'image',
        'is_file',
        'description',
        'group',
        'config',
        'is_active',
    ];

    protected $appends = [
        'image_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $searchFields = ['code'];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return Storage::disk(HasImage::disk())->url($this->image);
        }
        return null;
    }
}
