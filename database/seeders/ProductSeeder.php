<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\AttributeOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\StoreFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = Category::pluck('id', 'name');

        $seller_1 = Store::factory()->create()->id; // Roupa
        $seller_2 = Store::factory()->create()->id; // Eletronico

        $attributeTecido = Attribute::where('name', 'Tipo de tecido')->first();

        $attributeVolts = Attribute::where('name', 'Voltagem')
                                ->where('category_id', $categories['eletronicos'])->first();

        $attributeTipoEletronico = Attribute::where('name', 'Tipo')
                                ->where('category_id', $categories['eletronicos'])->first();

        
        $attributeTecidoOptions = $attributeTecido->attributeOptions()->pluck('id', 'label');
        $attributeVoltsOptions = $attributeVolts->attributeOptions()->pluck('id', 'label');
        $attributeTipoEletronicoOptions = $attributeTipoEletronico->attributeOptions()->pluck('id', 'label');

        $product_1 = Product::create([
            'store_id'          => $seller_1,
            'name'              => 'Camisa Polo Masculina',
            'slug'              => Str::slug('Camisa Polo Masculina ' . '-' . $seller_1),
            'description'       => 'Camisa polo masculina 100% algodao',
            'price'             => 159.90,
            'sku'               => Str::random(4),
            'stock_quantity'    => 56,
            'is_active'         => true,
            'category_id'       => $categories['roupas'],
            'weight'            => 100,
            'length'            => 0,
            'width'             => 45,
            'height'            => 80,
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        $product_2 = Product::create([
            'store_id'          => $seller_1,
            'name'              => 'Blusa Treino Hard Work',
            'slug'              => Str::slug('Camisa Treino Hard Work' . '-' . $seller_1),
            'description'       => 'Blusa 100% antibactérias feita pro seu treino',
            'price'             => 69.90,
            'sku'               => Str::random(4),
            'stock_quantity'    => 120,
            'is_active'         => true,
            'category_id'       => $categories['roupas'],
            'weight'            => 100,
            'length'            => 0,
            'width'             => 45,
            'height'            => 80,
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        $product_3 = Product::create([
            'store_id'          => $seller_2,
            'name'              => 'Playstation 5 Pro 2 Controles + God of war',
            'slug'              => Str::slug('Playstation 5 Pro 2 Controles + God of war' . '-' . $seller_2),
            'description'       => 'Descrição....',
            'price'             => 6850.50,
            'sku'               => Str::random(4),
            'stock_quantity'    => 30,
            'is_active'         => true,
            'category_id'       => $categories['eletronicos'],
            'weight'            => 300,
            'length'            => 30,
            'width'             => 12,
            'height'            => 32,
            'created_at'        => now(),
            'updated_at'        => now()
        ]);


        DB::table('products_attributes_value')->insert([
            [
                'product_id' => $product_1->id,
                'attribute_id' => $attributeTecido->id,
                'attribute_option_id' => $attributeTecidoOptions['Algodão'],
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $product_2->id,
                'attribute_id' => $attributeTecido->id,
                'attribute_option_id' => $attributeTecidoOptions['Poliéster'],
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $product_3->id,
                'attribute_id' => $attributeVolts->id,
                'attribute_option_id' => $attributeVoltsOptions['Bivolt'],
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $product_3->id,
                'attribute_id' => $attributeTipoEletronico->id,
                'attribute_option_id' => $attributeTipoEletronicoOptions['Console'],
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
