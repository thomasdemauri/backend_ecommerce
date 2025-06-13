<?php

namespace Tests\Unit;

use App\Exceptions\AttributeDoesNotExistsForCategory;
use Tests\TestCase;
use App\Models\Store;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function createCategoryWithAttributes(): Category
    {
        $category = Category::factory()->create();

        $attributes = Attribute::factory()->count(3)->create([
            'category_id' => $category->id,
            'required' => true
        ]);
        
        foreach ($attributes as $attribute) {
            AttributeOption::factory()->count(2)->create([
                'attribute_id' => $attribute->id,
            ]);
        }
                
        $category->load('attributes.attributeOptions');

        return $category;
    }

    private function generateAttributesPayload(Category $category)
    {
        return $category->attributes->map(function ($attribute) {

            $randomOption = $attribute->attributeOptions->random();
    
            return [
                'attribute_id' => $attribute->id,
                'attribute_option_id' => $randomOption->id
            ];

        })->toArray();
        
    }

    private function generatePayload(Store $store, Category $category)
    {        
        
        $selectedOptions = $this->generateAttributesPayload($category);

        return [
            'store_id' => $store->id,
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => $category->id,
            'attributes' => $selectedOptions
        ];

    }
    
    
    /**
     * Cria um produto com os atributos e opcoes corretas
     */
    public function test_store_product_successfully_with_valid_attributes(): void
    {
        $productService = new ProductService();
        
        $store = Store::factory()->create();

        $category = $this->createCategoryWithAttributes();
        $payload = $this->generatePayload($store, $category);
        
        $product = $productService->store($payload);

        $this->assertDatabaseHas('products', [
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => $category->id
        ]);

        $this->assertEquals('Monitor gamer 144Hz OLED', $product->name);
    }

    public function test_store_product_throws_attribute_does_not_exists_for_category_exception(): void 
    {

        $this->expectException(AttributeDoesNotExistsForCategory::class);

        $productService = new ProductService();

        $store = Store::factory()->create();
        $category = $this->createCategoryWithAttributes();

        $invalidAttribute = Attribute::factory()->create();

        $payload = [
            'store_id' => $store->id,
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => $category->id,
            'attributes' => [
                [
                    'attribute_id' => $invalidAttribute->id,
                    'attribute_option_id' => 1
                ]
            ]
        ];

        $productService->store($payload);
    }

    public function test_store_product_throws_option_does_not_exists_for_attribute_exception(): void {
        
    }
}
