<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{

    use HasFactory;


    protected $fillable = [
        'category_id',
        'name',
        'required',
        'type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attributeOptions()
    {
        return $this->hasMany(AttributeOption::class);
    }
}
