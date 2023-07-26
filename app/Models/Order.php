<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'title',
        'code',
        'status',
        'date',
        'customer_name',
        'customer_phone',
        'customer_id',
        'product_id',
        'note',
        'reason_rejected',
        'qty',
        'price',
        'total',
        'discount',
        'is_active',
    ];

    function details() {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    function customer() {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
