<?php

namespace App\Exceptions;

use Exception;

class RequiredAttributesMissing extends Exception
{
    public function __construct(string $missingIds)
    {
        parent::__construct("There are required attributes missing. Attributes ID's [{$missingIds}].");
    }
}
