<?php

namespace App\Exceptions;

use Exception;
use App\Models\Attribute;

class AttributeDoesNotExistsForCategory extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Attribute [{$id}] does not exist for this category.");
    }
}
