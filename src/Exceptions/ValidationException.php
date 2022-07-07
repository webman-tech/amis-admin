<?php

namespace Kriss\WebmanAmisAdmin\Exceptions;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public array $errors;

    public function __construct(array $errors, string $message = null)
    {
        $this->errors = $errors;
        parent::__construct($message ?? 'Validation Error', 422);
    }
}
