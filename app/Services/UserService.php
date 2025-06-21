<?php

namespace App\Services;

use App\Exceptions\AuthenticateFailedException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class UserService
{
    public function store(array $payload)
    {
        $user = User::create($payload);

        return $user;
    }

    /**
     * Faz a autenticaçao do usuário.
     * 
     * @param array $payload Contem email e senha.
     * 
     * @return NewAccessToken Token do usuário autenticado.
     */
    public function authenticate(array $payload): NewAccessToken
    {
        $user = User::where('email', $payload['email'])->first();

        if (!$user || !Hash::check($payload['password'], $user->password)) {
            throw new AuthenticateFailedException();
        }

        $token = $user->createToken('api');

        return $token;
    }

    
}