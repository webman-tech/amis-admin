<?php

namespace Kriss\WebmanAmisAdmin\Exceptions;

use RuntimeException;

class ActionDisableException extends RuntimeException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'No permission', 403);
    }
}