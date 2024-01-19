<?php

namespace WebmanTech\AmisAdmin\Contracts;

interface RepositoryInterface
{
    public function withRequest(RequestInterface $request): self;

    public function withValidator(ValidatorInterface $validator): self;
    
    public function addAction(string $name, string $action, array $config): self;

    public function updateAction(string $name, array $config, string $action = null): self;
}