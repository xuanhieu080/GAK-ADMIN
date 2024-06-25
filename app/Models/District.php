<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'full_name',
        'full_name_en',
        'code_name',
        'province_code',
        'province_id',
    ];

    public function ward()
    {
        return $this->hasMany(Ward::class, 'district_id', 'id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }
}
