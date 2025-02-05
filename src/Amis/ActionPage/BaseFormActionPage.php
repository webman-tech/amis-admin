<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

use WebmanTech\AmisAdmin\Model\AmisAttributeDefine;

class BaseFormActionPage extends BaseActionPage
{
    protected AmisAttributeDefine $attributeDefine;

    protected function dataSchema(): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        $data = $this->dataSchema();
        $form = $this->attributeDefine->getForm();

        if ($data !== null) {
            return [
                'data' => $data,
                'body' => $form,
            ];
        }
        return $form;
    }
}