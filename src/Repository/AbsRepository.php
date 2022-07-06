<?php

namespace Kriss\WebmanAmisAdmin\Repository;

use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use Kriss\WebmanAmisAdmin\Validator\NullValidator;
use Kriss\WebmanAmisAdmin\Validator\ValidatorInterface;

abstract class AbsRepository implements RepositoryInterface
{
    public const SCENE_CREATE = 'create';
    public const SCENE_UPDATE = 'update';

    protected string $primaryKey = 'id';
    protected ?ValidatorInterface $validator = null;

    /**
     * @inheritDoc
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @inheritdoc
     */
    public function getLabel(string $attribute): string
    {
        return $this->attributeLabels()[$attribute] ?? $attribute;
    }

    /**
     * $attribute => $label
     * @return array
     */
    protected function attributeLabels(): array
    {
        return [];
    }

    /**
     * grid 的关联 with
     * @return array
     */
    protected function gridRelations(): array
    {
        return [];
    }

    /**
     * gird 的查询的列数据
     * @return string[]
     */
    protected function gridColumns(): array
    {
        return ['*'];
    }

    /**
     * form 的查询字段
     * @return string[]
     */
    protected function formColumns(): array
    {
        return ['*'];
    }

    /**
     * detail 的查询字段
     * @return string[]
     */
    protected function detailColumns(): array
    {
        return ['*'];
    }

    /**
     * 验证字段
     * @return array 验证过后的字段
     * @throws ValidationException
     */
    protected function validate(array $data, string $scene): array
    {
        return $this->validator()->validate(
            $data,
            $this->getRules($scene),
            $this->getRuleMessages($scene),
            $this->getRuleCustomeAttributes($scene)
        );
    }

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function withValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    protected function validator(): ValidatorInterface
    {
        return $this->validator ?? new NullValidator();
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function getRules(string $scene): array
    {
        return [];
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function getRuleMessages(string $scene): array
    {
        return [];
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function getRuleCustomeAttributes(string $scene): array
    {
        return [];
    }
}