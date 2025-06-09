<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'required',
        'type'
    ];
}
