<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'content',
        'customer_id',
        'post_id',
        'is_active',
    ];

    /**
     * @return void
     */
    public function customer() {
        return $this->hasOne(Customer::class,'id', 'customer_id');
    }
    /**
     * @return void
     */
    public function post() {
        return $this->hasOne(Post::class,'id', 'post_id');
    }
}
