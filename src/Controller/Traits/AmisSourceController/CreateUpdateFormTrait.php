<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;

trait CreateUpdateFormTrait
{
    /**
     * 新增和修改的表单
     * @param string $scene
     * @return array
     */
    protected function form(string $scene): array
    {
        $repository = $this->repository();
        if ($repository instanceof HasPresetInterface) {
            return $repository->getPresetsHelper()->withScene($scene)->pickForm();
        }

        return [
            //Amis\FormField::make()->name('name'),
        ];
    }

    /**
     * @param array $formFields
     * @return array
     */
    protected function buildFormFields(array $formFields): array
    {
        foreach ($formFields as &$item) {
            if (is_string($item)) {
                $item = Amis\FormField::make()->name($item);
            }
            if (is_array($item)) {
                $item = Amis\FormField::make($item);
            }
            if ($item instanceof Amis\Component) {
                $item = $item->toArray();
            }
            if ($value = $item['label'] ?? $this->repository()->getLabel($item['name'])) {
                $item['label'] = $value;
            }
            if ($value = $item['labelRemark'] ?? $this->repository()->getLabelRemark($item['name'])) {
                $item['labelRemark'] = $value;
            }
            if ($value = $item['description'] ?? $this->repository()->getDescription($item['name'])) {
                $item['description'] = $value;
            }
        }
        unset($item);
        return $formFields;
    }
}
