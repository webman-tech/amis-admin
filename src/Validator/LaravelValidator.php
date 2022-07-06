<?php

namespace Kriss\WebmanAmisAdmin\Validator;

use Illuminate\Contracts\Validation\Factory;
use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;

class LaravelValidator implements ValidatorInterface
{
    protected Factory $factory;

    public function __construct(Factory $validator)
    {
        $this->factory = $validator;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        $validator = $this->factory->make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            $errors = array_map(fn($messages) => $messages[0], $validator->errors()->toArray());
            throw new ValidationException($errors);
        }
        return array_filter($data, fn($key) => array_key_exists($key, $rules), ARRAY_FILTER_USE_KEY);
    }
}