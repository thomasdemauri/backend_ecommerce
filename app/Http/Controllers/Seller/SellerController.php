<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Response;
use App\Services\SellerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\NewStoreRequest;

class SellerController extends Controller
{
    private SellerService $sellerService;

    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    /**
     * Registra o usuário como vendedor e cria a loja associada.
     *
     * Valida os dados recebidos e cria a loja, ativando o status de vendedor no usuário autenticado.
     *
     * @param NewStoreRequest $request Dados validados da loja
     * @return \Illuminate\Http\JsonResponse Mensagem de sucesso com status 201
     */
    public function createSellerWithStore(NewStoreRequest $request)
    {
        $storeData = $request->validated();

        $this->sellerService->createSellerWithStore($storeData);

        return response()->json([
            'message' => 'Vendedor criado com sucesso!'
        ], Response::HTTP_CREATED);
    }
}
