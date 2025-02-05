<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\DynamicPage;

use Webman\Http\Request;
use WebmanTech\AmisAdmin\Amis\Service\DynamicFormPage;
use WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController\CreateTrait;
use WebmanTech\AmisAdmin\Controller\Traits\AmisSourceController\CreateUpdateFormTrait;
use WebmanTech\AmisAdmin\Repository\AbsRepository;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;

class CreateFormPage extends DynamicFormPage
{
    use CreateUpdateFormTrait;

    protected HasPresetInterface $repository;

    public function withRepository(HasPresetInterface $repository)
    {

    }

    /**
     * @inheritDoc
     */
    public function handleSubmit(Request $request)
    {
        // TODO: Implement handleSubmit() method.
    }

    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        return $this->buildFormFields(
            $this->repository->getPresetsHelper()
                ->pickForm(AbsRepository::SCENE_CREATE)
        );
    }
}