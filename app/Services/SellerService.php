<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserIsAlreadyASeller;
use App\Exceptions\UserIsNotASellerException;
use App\Http\Resources\Store\StoreResource;
use App\Models\Store;

class SellerService
{

    private StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Marca o usuário autenticado como vendedor e cria uma nova loja associada.
     *
     * Este método verifica se o usuário já é vendedor, lança exceção caso positivo,
     * atualiza o status de vendedor e delega a criação da loja ao StoreService,
     * retornando o recurso da loja recém-criada.
     *
     * @param array $storeData Dados validados para criação da loja (ex: ['store_name' => string, ...])
     *
     * @return Store Model representando a loja criada
     *
     * @throws UserIsAlreadyASeller Exceção lançada se o usuário já for vendedor
     */
    public function createSellerWithStore(array $storeData): Store
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->ensureIsNotAlreadyASeller($user);

        $user->is_seller = true;
        $user->save();


        return $this->storeService->newStoreForUser($user ,$storeData);
    }

    public function ensureIsNotAlreadyASeller(User $user): void
    {
        if ($user->is_seller) {
            throw new UserIsAlreadyASeller($user->id);
        }
    }

    public function getAuthenticatedSeller(string $id): User
    {
        return User::with('store')
                        ->where('id', $id)
                        ->where('is_seller', true)
                        ->firstOrFail();
    }
}