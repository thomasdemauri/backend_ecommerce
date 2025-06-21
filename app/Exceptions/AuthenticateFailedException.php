<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class AuthenticateFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Credenciais inválidas.");
    }
}
