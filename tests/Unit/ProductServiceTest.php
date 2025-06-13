<?php

namespace Tests\Unit;

use App\Exceptions\AttributeDoesNotExistsForCategory;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use App\Exceptions\RequiredAttributesMissing;
use Tests\TestCase;
use App\Models\Store;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{

    use RefreshDatabase;

    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->productService = new ProductService();
    }

    /**
     * Cria uma categoria e já vincula atributos com suas
     * respectivas opções.
     * 
     * Todos os atributos são criados como 'REQUIRED'
     * 
     * @return Category
     */
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
    
    /**
     * Gera um array de atributos com sua respectiva opção selecionada.
     * 
     * Neste caso os atributos existem para a categoria assim como as
     * suas opções.
     * 
     * Para cada atributo escolhe uma opção aleatória válida.
     * 
     * @param Category $category Categoria no qual o payload irá se basear
     * para escolher os atributos e suas opções.
     * 
     * @return array Retorna um array PHP puro para inserir no payload.
     */
    private function generateAttributesPayload(Category $category): array
    {
        return $category->attributes->map(function ($attribute) {

            $randomOption = $attribute->attributeOptions->random();
    
            return [
                'attribute_id' => $attribute->id,
                'attribute_option_id' => $randomOption->id
            ];

        })->toArray();
        
    }

    /**
     * Usa a função generateAttributesPayload() para obter o 
     * array de atributos e retira o último para que assim fique faltando
     * pelo menos um atributo REQUIRED no payload.
     * 
     * @param Category $category Cateria no qual a função generateAttributesPayload()
     * vai utilizar.
     * 
     * @return array Retorna o array com a última posição excluída.
     * 
     */ 
    private function generateMissingRequiredAttributesPayload(Category $category): array
    {
        $requiredAttributes = $this->generateAttributesPayload($category);

        array_pop($requiredAttributes);

        return $requiredAttributes;
    }

    /**
     * Monta um payload com loja, categoria e atributos com suas respectivas
     * opções válido.
     * 
     * @param Store $store Loja que pertence o produto.
     * @param Category $category Categoria que pertence o produto.
     * 
     * @return array Retorna um array PHP puro com o payload montado.
     */
    private function generatePayload(Store $store, Category $category): array
    {        
        
        $attributes = $this->generateAttributesPayload($category);

        return [
            'store_id' => $store->id,
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => $category->id,
            'attributes' => $attributes
        ];

    }
    
    
    /**
     * Cria um produto com uma loja existente válida, atributos e suas respectivas opções
     * existentes.
     */
    public function test_store_product_successfully_with_valid_attributes(): void
    {        
        $store = Store::factory()->create();

        $category = $this->createCategoryWithAttributes();
        $payload = $this->generatePayload($store, $category);
        
        $product = $this->productService->store($payload);


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

    /**
     * Espera uma exceção caso um dos atributos selecionados não exista
     * para a categoria selecionada.
     */
    public function test_store_product_throws_attribute_does_not_exists_for_category_exception(): void 
    {

        $this->expectException(AttributeDoesNotExistsForCategory::class);

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

        $this->productService->store($payload);

    }

    /**
     * Espera uma exceção para caso uma das opções selecionadas
     * não exista para um determinado atributo.
     */
    public function test_store_product_throws_option_does_not_exists_for_attribute_exception(): void 
    {
        $this->expectException(OptionDoesNotExistsForAttribute::class);
        
        $store = Store::factory()->create();
        $category = $this->createCategoryWithAttributes();

        $randomAttribute = $category->attributes->random();
        

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
                    'attribute_id' => $randomAttribute->id,
                    'attribute_option_id' => 99999  // Non existing option
                ]
            ]
        ];

        $this->productService->store($payload);

    }

    /**
     * Espera uma exceção para caso no payload esteja faltando
     * atributos que são REQUIRED.
     */
    public function test_store_throws_exception_when_required_attributes_are_missing()
    {
        $this->expectException(RequiredAttributesMissing::class);   

        $store = Store::factory()->create();
        $category = $this->createCategoryWithAttributes();

        
        $payload = [
            'store_id' => $store->id,
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => $category->id,
            'attributes' => $this->generateMissingRequiredAttributesPayload($category),
        ];

        $this->productService->store($payload);

    }

    /**
     * Espera exceção quando categoria não existe.
     */
    public function test_store_throws_excepetion_when_invalid_category()
    {
        $this->expectException(ModelNotFoundException::class);

        $store = Store::factory()->create();
        $category = $this->createCategoryWithAttributes();

        
        $payload = [
            'store_id' => $store->id,
            'name' => 'Monitor gamer 144Hz OLED',
            'description' => 'O melhor para o sua gameplay',
            'price' => 1459.99,
            'stock_quantity' => 60,
            'is_active' => true,
            'category_id' => 99999,
            'attributes' => $this->generateMissingRequiredAttributesPayload($category),
        ];

        $this->productService->store($payload);

    }
}
