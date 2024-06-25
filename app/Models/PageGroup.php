<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageGroup extends Model
{
    use HasFactory;
    use Searchable, Filterable;

    protected $fillable = [
        'id',
        'name',
        'column',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected $searchFields = ['name'];

    public function details() {
        return  $this->hasMany(Page::class, 'group_id', 'id');
    }
}
