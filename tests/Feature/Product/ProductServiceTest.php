<?php

namespace Tests\Feature\Product;

use App\Exceptions\AttributeDoesNotExistsForCategory;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use Tests\TestCase;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Services\StoreService;
use App\Models\AttributeOption;
use App\Services\SellerService;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private $storeService;
    private $sellerService;
    private $categoryService;
    private $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storeService = $this->createMock(StoreService::class);
        $this->sellerService = $this->createMock(SellerService::class);
        $this->categoryService = $this->createMock(CategoryService::class);

        $this->productService = new ProductService(
            $this->storeService,
            $this->sellerService,
            $this->categoryService
        );
    }

    private function mockSellerAndCategory(User $user, Store $store, Category $category)
    {
        // Simula que está retornando um usuário autenticado.
        $this->sellerService->method('getAuthenticatedSeller')->willReturn($user->setRelation('store', $store));

        // Como productService chama esse método que carrega uma categoria com os seus atributos,
        // simula uma chamada também mas apenas retorna a própria categoria para simular.
        $this->categoryService->method('getCategoryWithAttributes')->willReturn($category);

    }

    public function test_store_product_successfully_with_valid_attributes()
    {
        // Arrange
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);

        $attribute = Attribute::factory()->create(['required' => true]);
        $option = AttributeOption::factory()->create([
            'attribute_id' => $attribute->id,
            'value' => 'algodao'
        ]);

        $category = Category::factory()->create();
        $category->attributes->push($attribute);
        $attribute->setRelation('attributeOptions', collect([$option]));
        $category->setRelation('attributes', collect([$attribute]));

        // Mockando os serviços
        $this->mockSellerAndCategory($user, $store, $category);

        // Payload do produto
        $payload = [
            'product' => [
                'name' => 'Camisa do Timão',
                'description' => 'Linda camisa',
                'price' => 199.99,
                'category_id' => $category->id,
                'stock_quantity' => 5,
                'is_active' => true
            ],
            'attributes' => [
                [
                    'attribute_id' => $attribute->id,
                    'attribute_option_id' => $option->id
                ]
            ]
        ];

        // Act
        $product = $this->productService->storeWithAttributes($payload, $user);

        // Assert
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Camisa do Timão', $product->name);
        $this->assertTrue($product->is_active);
        $this->assertCount(1, $product->productAttributeValues);
    }

    public function test_store_product_throws_expection_when_attribute_does_not_belong_to_category()
    {

        // Erro esperado
        $this->expectException(AttributeDoesNotExistsForCategory::class);

        // Arrange
        $user = User::factory()->create();
        $store = Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();

        // Mocka os serviços
        $this->mockSellerAndCategory($user, $store, $category);
        
        // Simula que não carrega nada ao acessar atributtes.
        $category->setRelation('attributes', collect());

        // Payload do produto
        $payload = [
            'product' => [
                'name' => 'Camisa do Timão',
                'description' => 'Linda camisa',
                'price' => 199.99,
                'category_id' => $category->id,
                'stock_quantity' => 5,
                'is_active' => true
            ],
            'attributes' => [
                [
                    'attribute_id' => 999,
                    'attribute_option_id' => 999
                ]
            ]
        ];

        // Act
        $this->productService->storeWithAttributes($payload, $user);

    }

    public function test_store_product_throws_expection_when_option_does_not_belong_to_attribute()
    {
        // Assert
        $this->expectException(OptionDoesNotExistsForAttribute::class);

        $user = User::factory()->create();
        $store = Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();
        
        // Mock
        $this->mockSellerAndCategory($user, $store, $category);

        $attribute = Attribute::factory()->create([
            'category_id' => $category->id
        ]);

        $category->setRelation('attributes', $attribute);
        $attribute->setRelation('attributeOptions', collect());
        

        // Payload do produto
        $payload = [
            'product' => [
                'name' => 'Camisa do Timão',
                'description' => 'Linda camisa',
                'price' => 199.99,
                'category_id' => $category->id,
                'stock_quantity' => 5,
                'is_active' => true
            ],
            'attributes' => [
                [
                    'attribute_id' => $attribute->id,
                    'attribute_option_id' => 999
                ]
            ]
        ];

        // Act
        $this->productService->storeWithAttributes($payload, $user);

    }
}
