<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, Searchable, Filterable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'description',
        'created_at',
    ];
}
