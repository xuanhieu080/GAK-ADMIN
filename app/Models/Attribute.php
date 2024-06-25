<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;
    use Searchable, Filterable;

    protected $fillable = [
        'name',
        'group_id',
        'color',
        'link',
        'is_color',
    ];
    protected $casts = [
        'is_color' => 'boolean',
    ];
    protected $appends = [
        'slug','title'
    ];

    protected $searchFields = ['name'];

    public function group()
    {
        return $this->hasOne(AttributeGroup::class, 'id', 'group_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class, 'attribute_id', 'id');
    }

    public function getSlugAttribute() {
        return \Str::slug($this->name);
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }
}
