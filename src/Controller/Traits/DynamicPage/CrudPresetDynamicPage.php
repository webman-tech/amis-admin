<?php

namespace WebmanTech\AmisAdmin\Controller\Traits\DynamicPage;

use Webman\Http\Request;
use WebmanTech\AmisAdmin\Amis\Service\DynamicFormPage;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;
use WebmanTech\AmisAdmin\Repository\AbsRepository;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;

class CrudPresetDynamicPage extends DynamicFormPage
{
    protected AbsRepository $repository;
    protected string $scene;

    public function withPresetsHelper(AbsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function handleSubmit(Request $request)
    {
        throw new \InvalidArgumentException();
    }

    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        if ($this->repository instanceof HasPresetInterface) {
            return $this->repository->getPresetsHelper()->pickForm($this->scene);
        }
    }
}