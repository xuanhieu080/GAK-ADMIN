<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_group_id',
        'attribute_id',
        'is_active',
        'priority',
        'id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    protected $appends = [
        'attribute_group_name'
    ];

    public function attribute() {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }

    public function attributeGroup() {
        return $this->hasOne(AttributeGroup::class, 'id', 'attribute_group_id');
    }

    public function product() {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getAttributeGroupNameAttribute()
    {
        return object_get($this,'attributeGroup.name');
    }
}
