<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_id',
        'color',
        'link',
        'is_color',
    ];

    public function group(){
        return $this->hasOne(AttributeGroup::class, 'id', 'group_id');
    }
}
