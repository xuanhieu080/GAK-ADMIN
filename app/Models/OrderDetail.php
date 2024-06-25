<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'product_variant_id',
        'product_variant_name',
        'product_variant_code',
        'option_name',
        'note',
        'qty',
        'price',
        'total',
        'discount',
        'cost'
    ];

    function product() {
        return $this->hasOne(Product::class,'id', 'product_id');
    }
}
