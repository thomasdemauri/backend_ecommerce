<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\RequiredAttributesMissing;
use App\Exceptions\OptionDoesNotExistsForAttribute;
use App\Exceptions\AttributeDoesNotExistsForCategory;
use App\Exceptions\UserIsNotSellerException;

class ProductService
{

    private StoreService $storeService;
    private SellerService $sellerService;
    private CategoryService $categoryService;

    private $optionalFields = [
        'is_active',
        'weight',
        'length',
        'width',
        'height',
        'stock_quantity',
        'sku'
    ];

    public function __construct(
        StoreService $storeService, 
        SellerService $sellerService,
        CategoryService $categoryService
    )
    {
        $this->storeService = $storeService;
        $this->sellerService = $sellerService;
        $this->categoryService = $categoryService;
    }

    public function detail(string $id, User $user)
    {

        $store = $user->store;

        $product = Product::with([
            'productAttributeValues.attributeOption', 
            'productAttributeValues.attribute',
            'store',
            'category'
        ])->where('store_id', $store->id)->findOrFail($id);

        return $product;
    }

    /**
     * Cria produto com seus devidos atributos.
     * 
     * @param array $payload Contem os dados do produto
     * e de seus atributos com suas respectivas opções. 
     * 
     * @param User $user Usuário autenticado
     * 
     * @throws RequiredAttributesMissing Lança exceção quando estiver faltando 
     * atributo requerido no corpo da requisição.
     * 
     * @return Product Retorna produto criado.
     */
    public function storeWithAttributes(array $payload, User $user): Product
    {
        
        try {
            
            if ($user->is_seller == false) {
                throw new UserIsNotSellerException($user->id);
            }

            DB::beginTransaction();
            
            $category = $this->categoryService->getCategoryWithAttributes($payload['product']['category_id']);
            $product = $this->createProductWithoutAttributes($payload['product'], $user);
            
            $this->saveAttributes($product, $category, $payload['attributes']);
            $this->checkRequiredAttributesInPayload($payload['attributes'], $category);
            
            DB::commit();
            
            $product->refresh();
            $product->load(['productAttributeValues.attribute',
                'productAttributeValues.attributeOption',
                'store'
            ]);
            $product->setRelation('category', $category);   // Evita carregar novamente
            
            return $product;

        } catch (Exception $exception) {

            DB::rollBack();
            throw $exception;

        }

    }

    /**
     * Salva os atributos do produto.
     * 
     * @param Product $product Model no qual os atributos serão salvos.
     * 
     * @param Category $category Categoria do produto.
     * 
     * @param array $attributes Atributos vindo do payload.
     * 
     * @return void Apenas salva no model.
     * 
     * @throws AttributeDoesNotExistsForCategory Lança exceção quando atributo não corresponder a categoria.
     * 
     * @throws OptionDoesNotExistsForAttribute Lança exceção quando opção não corresponder a atributo.
     * 
     */
    private function saveAttributes(Product $product, Category $category, array $attributes): void
    {
        foreach ($attributes as $attributeRequest) {
                                
            $attribute = $category->attributes->firstWhere('id', $attributeRequest['attribute_id']);
            
            if (!$attribute) {
                throw new AttributeDoesNotExistsForCategory($attributeRequest['attribute_id']);
            }

            if ($attribute->type === 'options') {

                $selectedOption = $attribute->attributeOptions->firstWhere('id', $attributeRequest['attribute_option']);
                
                if (!$selectedOption) {
                    throw new OptionDoesNotExistsForAttribute($attributeRequest['attribute_option'], $attribute->id);
                }

                $product->productAttributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'attribute_option_id' => $selectedOption->id,
                    'value' => $selectedOption->value
                ]);

            } else {

                $product->productAttributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'value' => $attributeRequest['attribute_option']
                ]);

            }

            

        }

        return;
    }


    /**
     * Verifica os IDs requeridos e compara com os ids presente no payload.
     * 
     * Caso esteja faltando algum lança exceção, senão retorna false.
     * 
     * @param array $attributesPayload Array contendo os atributos do payload.
     * @param array $requiredAttributes Array contendo os atributos requeridos.
     * 
     * @return false Caso nenhum atributo requerido esteja faltando.
     * 
     * @throws RequiredAttributesMissing Quando estiver faltando atributos requeridos.
     */
    private function checkRequiredAttributesInPayload(array $attributesPayload, Category $category): void
    {

        $requiredAttributesIds = $category->attributes->where('required', true)->pluck('id')->toArray();

        $attributesIdsFromPayload = array_map(function ($attribute) {
            return $attribute['attribute_id'];
        }, $attributesPayload);

        $missingRequiredAttributeIds = array_diff($requiredAttributesIds, $attributesIdsFromPayload);

        if (!empty($missingRequiredAttributeIds)) {
            $ids = implode(',', $missingRequiredAttributeIds);
            throw new RequiredAttributesMissing($ids);
        }

        return;
    } 
    
    /**
     * Cria apenas o produtos sem os atributos.
     */
    private function createProductWithoutAttributes(array $productData, User $seller): Product
    {
        $data = [
            'store_id' => $seller->store->id,
            'name' => $productData['name'],
            'slug' => Str::slug($productData['name'] . '-' . $seller->store->id),
            'description' => $productData['description'],
            'price' => $productData['price'],
            'category_id' => $productData['category_id'],
        ];

        $data+= $this->filterOptionalFields($productData);

        return Product::create($data);
    }
    
    /**
     * Retorna apenas as chaves que estão presentes no payload e 
     * são opcionais também.
     * 
     * Isso evita que no banco de dados salve como null os campos
     * que tenham regras como default(value).
     */
    private function filterOptionalFields(array $productData): array
    {
        $fields = [];

        foreach ($this->optionalFields as $field) {
            if (array_key_exists($field, $productData)) {
                $fields[$field] = $productData[$field];
            }
        }

        return $fields;
    }


}