<?php

namespace Tests\Feature\Product;

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
use App\Exceptions\UserIsNotSellerException;
use App\Exceptions\RequiredAttributesMissing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use App\Exceptions\AttributeDoesNotExistsForCategory;

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

    private function mockCategory(Category $category)
    {

        /**
         * ProductService espera um paylod que contenha um category_id onde é passado para 
         * getCategoryWithAttributes. Nesse caso mockamos para que não dê erro já que 
         * não existe esse payload passando o id da categoria.
         */
        $this->categoryService->method('getCategoryWithAttributes')->willReturn($category);

    }

    public function test_store_product_successfully_with_valid_attributes()
    {
        // Arrange
        $user = User::factory()->create([
            'is_seller' => true
        ]);

        Store::factory()->create(['user_id' => $user->id]);

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
        $this->mockCategory($category);

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
                    'attribute_option' => $option->id
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
        $user = User::factory()->create([
            'is_seller' => true
        ]);

        Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();

        // Mocka os serviços
        $this->mockCategory($category);
        
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
                    'attribute_option' => 999
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

        // Arrange
        $user = User::factory()->create([
            'is_seller' => true
        ]);

        Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();
        
        // Mock
        $this->mockCategory($category);

        $attribute = Attribute::factory()->create([
            'category_id' => $category->id,
            'type' => 'options'
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
                    'attribute_option' => 999
                ]
            ]
        ];

        // Act
        $this->productService->storeWithAttributes($payload, $user);

    }

    public function test_store_product_throws_expection_when_required_attributes_are_missing()
    {
        // Assert
        $this->expectException(RequiredAttributesMissing::class);

        // Arrange
        $user = User::factory()->create([
            'is_seller' => true
        ]);

        Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();
        $this->mockCategory($category);

        $attribute = Attribute::factory()->create([
            'category_id' => $category->id,
            'required' => true
        ]);

        $category->setRelation('attributes', $attribute);

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
            'attributes' => []
        ];

        // Act 
        $this->productService->storeWithAttributes($payload, $user);

    }

    public function test_store_product_successfully_with_text_attributes()
    {
        // Arrange
        $user = User::factory()->create([
            'is_seller' => true
        ]);

        Store::factory()->create([
            'user_id' => $user->id
        ]);

        $category = Category::factory()->create();
        $this->mockCategory($category);
        
        $textAttribute = Attribute::factory()->create([
            'type' => 'text'
        ]);

        $category->setRelation('attributes', $textAttribute);

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
                    'attribute_id' => $textAttribute->id,
                    'attribute_option' => 'Intel I5'
                ]
            ]
        ];
        
        // Act
        $product = $this->productService->storeWithAttributes($payload, $user);

        // Assert
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Camisa do Timão', $product->name);
        $this->assertCount(1, $product->productAttributeValues);
        $this->assertEquals('Intel I5', $product->productAttributeValues->first()->value ?? '');

    }
    
    public function test_store_product_throws_expection_when_user_is_not_a_seller()
    {

        // Arrange
        $user = User::factory()->create([
            'is_seller' => false
        ]);

        // Assert
        $this->expectException(UserIsNotSellerException::class);

        // Act
       $this->productService->storeWithAttributes([], $user);

    }
}
