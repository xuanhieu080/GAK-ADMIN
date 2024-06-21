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

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'id',
        'code',
        'status',
        'date',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_id',
        'user_id',
        'user_name',
        'user_phone',
        'product_id',
        'note',
        'address',
        'province_id',
        'district_id',
        'ward_id',
        'total',
        'discount',
        'shipping_cos',
        'savings',
        'payment_method',
    ];

    function details() {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    function customer() {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    function ward() {
        return $this->hasOne(Ward::class, 'id', 'ward_id');
    }

    function district() {
        return $this->hasOne(District::class, 'id', 'district_id');
    }

    function province() {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }
}
