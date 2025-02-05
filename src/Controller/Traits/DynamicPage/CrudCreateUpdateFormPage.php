<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\DynamicPage;

use Webman\Http\Request;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\Service\DynamicFormPage;

class CrudCreateUpdateFormPage extends DynamicFormPage
{
    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        return [
            FormField::make()
        ];
    }

    /**
     * @inheritDoc
     */
    public function handleSubmit(Request $request)
    {
        // TODO: Implement handleSubmit() method.
    }
}