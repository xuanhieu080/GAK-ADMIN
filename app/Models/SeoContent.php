<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoContent extends Model
{
    use HasFactory;
    use Searchable, Filterable;

    protected $fillable = [
        'link',
        'description',
        'is_active',
        'user_id'
    ];
}
