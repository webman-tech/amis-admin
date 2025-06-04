<?php

namespace WebmanTech\AmisAdmin\Validator;

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