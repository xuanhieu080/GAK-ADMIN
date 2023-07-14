<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    const path = 'posts';

    protected $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'image',
        'author_id',
        'comment_count',
        'is_active'
    ];

    /**
     * @return void
     */
    public function author() {
        return $this->hasOne(User::class,'id', 'author_id');
    }
}
