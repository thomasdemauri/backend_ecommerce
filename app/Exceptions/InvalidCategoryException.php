<?php

namespace App\Exceptions;

use Exception;

class InvalidCategoryException extends Exception
{
    public function __construct(string $categoryId ,string $categoryName)
    {
        parent::__construct("Category [{$categoryId}] {$categoryName} does not exist.");
    }
}
