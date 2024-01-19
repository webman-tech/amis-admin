<?php

namespace WebmanTech\AmisAdmin\Impl;

use WebmanTech\AmisAdmin\Contracts\ValidatorInterface;

class NullValidator implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return $data;
    }
}