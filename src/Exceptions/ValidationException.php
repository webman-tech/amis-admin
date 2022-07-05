<?php

namespace Kriss\WebmanAmisAdmin\Exceptions;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation Error', 422);
    }
}
