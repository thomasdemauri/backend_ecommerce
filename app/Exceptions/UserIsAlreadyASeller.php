<?php

namespace App\Exceptions;

use Exception;

class UserIsAlreadyASeller extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Usuário {$id} já é um vendedor.");
    }
}
