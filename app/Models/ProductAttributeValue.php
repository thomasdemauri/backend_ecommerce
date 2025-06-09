<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',         // Ex. voltagem
        'attribute_option_id',  // 220  
        'value'                 // Ou campo livre
    ];

    public function attributeOption()
    {
        return $this->belongsTo(AttributeOption::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
