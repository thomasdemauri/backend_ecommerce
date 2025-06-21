<?php

namespace App\Services;

use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Str;

class StoreService
{

    /**
     * Cria uma nova loja para usuário.
     * 
     * @param User $user Usuário que pertencerá a loja.
     * 
     * @param array #StoreData Array contendo as informações para criacão
     * da loja.
     * 
     * @return Store Retorna model Store.
     */
    public function newStoreForUser(User $user, array $storeData): Store
    {
        return Store::create([
            'user_id' => $user->id,
            'store_name' => $storeData['store_name'],
            'slug' => Str::slug($storeData['store_name'])
        ]);
    }

}