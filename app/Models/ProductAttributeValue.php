<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'products_attributes_value';
    
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
