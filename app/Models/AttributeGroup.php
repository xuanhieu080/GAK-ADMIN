<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    use HasFactory;
    use Searchable, Filterable;


    protected $fillable = [
        'name',
        'name_en',
        'priority',
        'is_color',
        'link',
        'link_en',
        'is_main',
    ];
    protected $casts = [
        'is_color' => 'boolean',
        'is_main' => 'boolean',
    ];
    protected $appends = [
        'title', 'slug',
        'title_en', 'slug_en',
    ];
    protected $searchFields = ['name'];

    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'group_id', 'id');
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function getSlugAttribute()
    {
        return \Str::slug($this->name);
    }

    public function getTitleEnAttribute()
    {
        return $this->name_en;
    }

    public function getSlugEnAttribute()
    {
        return \Str::slug($this->name_en);
    }
}
