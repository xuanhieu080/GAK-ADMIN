<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantDetail extends Model
{
    use HasFactory;
    use Searchable, Filterable;
    protected $fillable = [
        'variant_id',
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
}
