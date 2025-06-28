<?php

namespace App\Exceptions;

use Exception;

class UserIsNotSellerException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Usuário {$id} não é um vendedor.");
    }
}
