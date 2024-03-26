<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRecharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'description',
        'bank_id',
        'date',
        'customer_id',
        'user_id',
        'status'
    ];

    function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }
}
