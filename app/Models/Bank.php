<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    use Searchable, Filterable;

    protected $searchFields = ['name', 'short_name', 'code', 'logo'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
}
