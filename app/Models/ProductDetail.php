<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'description',
        'product_id',
        'is_active',
        'status',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
