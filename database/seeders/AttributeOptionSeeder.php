<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttributeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = Attribute::pluck('id', 'name');

        $options = [
            
            'roupas' => [
                'Tipo de tecido'    => ['Algodão', 'Poliéster', 'Jeans', 'Couro'],
            ],

            'eletronicos' => [
                'Voltagem'          => ['110v', '220v', 'Bivolt'],
                'Tipo'              => ['Console', 'Computador', 'Notebook', 'Celular', 'Monitor']
            ],

            'beleza e cuidados pessoais' => [
                'Tipo'              => ['Hidratante', 'Maquiagem', 'Perfume']
            ]
        ];

        foreach ($options as $categoryName => $attributes) {
            
            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            foreach ($attributes as $attributeName => $values) {

                $attribute = Attribute::where('category_id', $category->id)
                                        ->where('name', $attributeName)->first();

                if (!$attribute) {
                    continue;
                }

                foreach ($values as $value) {
                    DB::table('attribute_options')->insert([
                        'attribute_id'  => $attribute->id,
                        'label'         => $value,
                        'value'         => strtolower(str_replace(' ', '_', $value)),
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }

            }

        }
    }
}
