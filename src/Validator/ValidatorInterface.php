<?php

namespace WebmanTech\AmisAdmin\Validator;

use WebmanTech\AmisAdmin\Exceptions\ValidationException;

interface ValidatorInterface
{
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array 验证过后的字段值
     * @throws ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array;
}