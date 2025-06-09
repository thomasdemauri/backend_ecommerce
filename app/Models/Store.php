<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'store_image_url'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
