<?php

namespace Kriss\WebmanAmisAdmin\Repository;

use Kriss\WebmanAmisAdmin\Exceptions\ValidationException;
use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use Kriss\WebmanAmisAdmin\Validator\NullValidator;
use Kriss\WebmanAmisAdmin\Validator\ValidatorInterface;

abstract class AbsRepository implements RepositoryInterface
{
    public const SCENE_LIST = 'list';
    public const SCENE_DETAIL = 'detail';
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
     * @inheritdoc
     */
    public function getLabelRemark(string $attribute)
    {
        return $this->attributeLabelRemarks()[$attribute] ?? null;
    }

    /**
     * $attribute => $labelRemark
     * @return array
     */
    protected function attributeLabelRemarks(): array
    {
        return [];
    }

    /**
     * 获取需要隐藏的字段
     * @param string $scene
     * @return array
     */
    protected function hiddenAttributes(string $scene): array
    {
        return [];
    }

    /**
     * 获取默认隐藏后需要展示的字段
     * @param string $scene
     * @return array
     */
    protected function visibleAttributes(string $scene): array
    {
        return [];
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
            $this->rules($scene),
            $this->ruleMessages($scene),
            $this->ruleCustomAttributes($scene)
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
        if ($this->validator) {
            return $this->validator;
        }
        if ($validator = ConfigHelper::get('validator')) {
            $this->validator = call_user_func($validator);
        } else {
            $this->validator = new NullValidator();
        }

        return $this->validator;
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function rules(string $scene): array
    {
        return [];
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function ruleMessages(string $scene): array
    {
        return [];
    }

    /**
     * @param string $scene
     * @return array
     */
    protected function ruleCustomAttributes(string $scene): array
    {
        return $this->attributeLabels();
    }
}