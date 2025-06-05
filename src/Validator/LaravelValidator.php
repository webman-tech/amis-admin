<?php

namespace WebmanTech\AmisAdmin\Validator;

use Illuminate\Contracts\Validation\Factory;
use Webman\Http\UploadFile;
use WebmanTech\AmisAdmin\Exceptions\ValidationException;
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

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
        foreach ($data as &$value) {
            if ($value instanceof UploadFile) {
                $value = LaravelUploadedFile::wrapper($value);
            }
        }
        unset($value);

        $validator = $this->factory->make($data, $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            $errors = array_map(fn($messages) => $messages[0], $validator->errors()->toArray());
            throw new ValidationException($errors);
        }
        return $validator->validated();
    }
}
