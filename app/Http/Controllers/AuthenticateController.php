<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\StoreUserRequest;
use App\Exceptions\AuthenticateFailedException;
use App\Http\Requests\User\AuthenticateUserRequest;

class AuthenticateController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function authenticate(AuthenticateUserRequest $request)
    {
        $payload = $request->validated();

        try {

            $token = $this->userService->authenticate($payload);
            
        } catch (AuthenticateFailedException $e) {

            return response()->json([
                'Credenciais inválidas.'
            ], Response::HTTP_UNAUTHORIZED);

        }

        return response()->json([
            'token' => $token->plainTextToken
        ], Response::HTTP_OK);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.'], Response::HTTP_OK);
    }
}
