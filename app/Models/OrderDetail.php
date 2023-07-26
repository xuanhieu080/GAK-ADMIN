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
        'product_detail_id'
    ];

    function product() {
        return $this->hasOne(Product::class,'id', 'product_id');
    }

    function productDetail() {
        return $this->hasOne(ProductDetail::class,'id', 'product_detail_id');
    }
}
