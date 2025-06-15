<?php

namespace App\Exceptions;

use Exception;

class OptionDoesNotExistsForAttribute extends Exception
{
    public function __construct(string $idOption, string $idAttribute)
    {
        parent::__construct("Unable to find an existing ID [{$idOption}] option for this attribute. Attribute ID: [{$idAttribute}].");
    }

    public function render()
    {
        return response()->json([
            'message' => $this->getMessage()
        ], 422);
    }
}
