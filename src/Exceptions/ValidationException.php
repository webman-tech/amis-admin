<?php

namespace WebmanTech\AmisAdmin\Exceptions;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public function __construct(public array $errors, ?string $message = null)
    {
        parent::__construct($message ?? 'Validation Error', 422);
    }
}
