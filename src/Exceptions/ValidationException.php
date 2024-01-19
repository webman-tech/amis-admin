<?php

namespace WebmanTech\AmisAdmin\Exceptions;

use RuntimeException;
use WebmanTech\AmisAdmin\Contracts\ResourceOperateExceptionInterface;

class ValidationException extends RuntimeException implements ResourceOperateExceptionInterface
{
    public array $errors;

    public function __construct(array $errors, string $message = null)
    {
        $this->errors = $errors;
        parent::__construct($message ?? 'Validation Error', 422);
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return [];
    }
}
