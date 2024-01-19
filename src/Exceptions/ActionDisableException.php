<?php

namespace WebmanTech\AmisAdmin\Exceptions;

use RuntimeException;

class ActionDisableException extends RuntimeException
{
    public function __construct(string $action)
    {
        parent::__construct($action . ' no permission', 403);
    }
}