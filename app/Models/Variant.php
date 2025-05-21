<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    use Searchable, Filterable;

    protected $fillable = [
        'product_id',
        'attribute_group_id',
        'attribute_id',
        'is_active',
        'priority',
        'id',
        'is_hot',
        'is_main',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_color'  => 'boolean',
        'is_hot'    => 'boolean',
        'is_main'   => 'boolean',
    ];


    protected $appends = [
        'attribute_group_name',
        'attribute_group_slug',
        'attribute_group_link',
        'attribute_name',
        'attribute_slug',
        'attribute_group_name_en',
        'attribute_group_slug_en',
        'attribute_group_link_en',
        'attribute_name_en',
        'attribute_slug_en',
        'is_color',
    ];

    public function attribute()
    {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }

    public function attributeGroup()
    {
        return $this->hasOne(AttributeGroup::class, 'id', 'attribute_group_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getAttributeGroupNameAttribute()
    {
        return object_get($this, 'attributeGroup.name');
    }

    public function getAttributeGroupSlugAttribute()
    {
        return \Str::slug(object_get($this, 'attributeGroup.name'));
    }

    public function getAttributeNameAttribute()
    {
        return object_get($this, 'attribute.name');
    }

    public function getAttributeSlugAttribute()
    {
        return \Str::slug(object_get($this, 'attribute.name'));
    }

    public function getAttributeGroupNameEnAttribute()
    {
        return object_get($this, 'attributeGroup.name_en');
    }

    public function getAttributeGroupSlugEnAttribute()
    {
        return \Str::slug(object_get($this, 'attributeGroup.name_en'));
    }

    public function getAttributeNameEnAttribute()
    {
        return object_get($this, 'attribute.name_en');
    }

    public function getAttributeSlugEnAttribute()
    {
        return \Str::slug(object_get($this, 'attribute.name_en'));
    }

    public function getIsColorAttribute()
    {
        return filter_var(object_get($this, 'attributeGroup.is_color'), FILTER_VALIDATE_BOOLEAN);
    }

    public function getAttributeGroupLinkAttribute()
    {
        return object_get($this, 'attributeGroup.link');
    }

    public function getAttributeGroupLinkEnAttribute()
    {
        return object_get($this, 'attributeGroup.link_en');
    }
}
