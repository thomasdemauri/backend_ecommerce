<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        DB::table('attributes')->insert([

            // Roupas
            [
                'category_id'   => $categories['roupas'],
                'name'          => 'Tipo de tecido',
                'required'      => true,
                'type'          => 'options',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            // Eletronicos
            [
                'category_id'   => $categories['eletronicos'],
                'name'          => 'Voltagem',
                'required'      => true,
                'type'          => 'options',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'category_id'   => $categories['eletronicos'],
                'name'          => 'Tipo',
                'required'      => true,
                'type'          => 'options',
                'created_at'    => now(),
                'updated_at'    => now()
            ],       
            // Casa e decoração     
            [
                'category_id'   => $categories['beleza e cuidados pessoais'],
                'name'          => 'Tipo',
                'required'      => true,
                'type'          => 'options',
                'created_at'    => now(),
                'updated_at'    => now()
            ], 
        ]);
    }
}
