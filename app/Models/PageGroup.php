<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'column',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
