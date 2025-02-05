<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\Common;

use WebmanTech\AmisAdmin\Amis;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class RepositoryFormBuildSupport
{
    /**
     * 构建表单字段
     * @param array $formFields
     * @return array
     */
    protected function buildFormFields(array $formFields): array
    {
        $repository = null;
        if (property_exists($this, 'repository') && $this->repository instanceof AbsRepository) {
            $repository = $this->repository;
        }
        if ($repository === null && method_exists($this, 'repository') && ($r = $this->repository()) instanceof AbsRepository) {
            $repository = $r;
        }

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
            if ($repository !== null) {
                if ($value = $item['label'] ?? $repository->getLabel($item['name'])) {
                    $item['label'] = $value;
                }
                if ($value = $item['labelRemark'] ?? $repository->getLabelRemark($item['name'])) {
                    $item['labelRemark'] = $value;
                }
                if ($value = $item['description'] ?? $repository->getDescription($item['name'])) {
                    $item['description'] = $value;
                }
            }
        }
        unset($item);
        return $formFields;
    }
}