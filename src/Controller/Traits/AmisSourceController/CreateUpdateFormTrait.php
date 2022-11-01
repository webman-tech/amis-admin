<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController;

use WebmanTech\AmisAdmin\Amis;

trait CreateUpdateFormTrait
{
    /**
     * 新增和修改的表单
     * @param string $scene
     * @return array
     */
    protected function form(string $scene): array
    {
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
            $item['label'] = $item['label'] ?? $this->repository()->getLabel($item['name']);
            $item['labelRemark'] = $item['labelRemark'] ?? $this->repository()->getLabelRemark($item['name']);
        }
        unset($item);
        return $formFields;
    }
}