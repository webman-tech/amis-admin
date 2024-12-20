<?php

namespace WebmanTech\AmisAdmin\Repository;

use WebmanTech\AmisAdmin\Exceptions\ValidationException;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;
use WebmanTech\AmisAdmin\Validator\NullValidator;
use WebmanTech\AmisAdmin\Validator\ValidatorInterface;

abstract class AbsRepository implements RepositoryInterface
{
    protected ?string $primaryKey = null;
    protected ?ValidatorInterface $validator = null;

    /**
     * @inheritDoc
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey ?? 'id';
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
     * @inheritdoc
     */
    public function getDescription(string $attribute)
    {
        return $this->attributeDescriptions()[$attribute] ?? null;
    }

    /**
     * $attribute => $labelRemark
     * @return array
     */
    protected function attributeDescriptions(): array
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
     * @inheritDoc
     */
    public function create(array $data): void
    {
        $data = $this->validate($data, static::SCENE_CREATE);
        $this->doCreate($data);
    }

    /**
     * @param array $data
     */
    abstract protected function doCreate(array $data): void;

    /**
     * @inheritDoc
     */
    public function update(array $data, $id): void
    {
        $data = $this->validate($data, static::SCENE_UPDATE);
        $this->doUpdate($data, $id);
    }

    /**
     * @param array $data
     * @param $id
     */
    abstract protected function doUpdate(array $data, $id): void;

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