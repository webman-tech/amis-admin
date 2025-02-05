<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

use WebmanTech\AmisAdmin\Amis\Crud;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Amis\GridColumnActions;
use WebmanTech\AmisAdmin\Amis\Page;
use WebmanTech\AmisAdmin\Controller\AmisDynamicPageController;
use WebmanTech\AmisAdmin\Model\AmisAttributeDefine;

class CrudIndexActionPage extends BaseActionPage
{
    protected string $modelClass;
    protected AmisAttributeDefine $attributeDefine;
    protected AmisDynamicPageController $dynamicPageController;
    protected ActionPageProvider $actionPageProvider;

    /**
     * @inheritDoc
     */
    protected function pageSchema()
    {
        $createAction = $this->actionPageProvider->getActionByModel($this->modelClass, 'create');
        if ($createAction instanceof ServicePageInterface) {
            $createForm = $createAction->serviceSchema();
        } else {
            $createForm = $createAction->pageSchema();
        }

        $detailAction = $this->actionPageProvider->getActionByModel($this->modelClass, 'detail');
        if ($createAction instanceof ServicePageInterface) {
            $createForm = $createAction->serviceSchema();
        } else {
            $createForm = $createAction->pageSchema();
        }

        $crud = Crud::make()
            ->schema([
                'primaryKey' => $this->attributeDefine->getPrimaryKey(),
                'api' => $this->actionPageProvider->getHandleApiByModel($this->modelClass, 'index'),
            ])
            ->withColumns(array_merge(
                $this->attributeDefine->getGrid(),
                [
                    GridColumn::make()
                        ->type('operation')
                        ->label('操作')
                        ->schema([
                            'buttons' => [

                            ],
                        ])
                ]
            ))
            ->withCreate(
                $this->actionPageProvider->getHandleApiByModel($this->modelClass, 'create'),
                $createForm
            );

        $page = Page::make()
            ->withBody(50, $crud);
        $this->callExt('pageExt', $page);

        return $page;
    }

    protected function buildCrud()
    {

    }
}