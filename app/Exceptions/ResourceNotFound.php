<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ResourceNotFound extends Exception
{
    protected $code = 404;
    protected $message = "Recurso não encontrado.";

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->getCode()
        ], $this->getCode());
    }
}
