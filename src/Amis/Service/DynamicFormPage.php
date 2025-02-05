<?php

namespace WebmanTech\AmisAdmin\Amis\Service;

use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\AmisAdmin\Controller\AmisDynamicPageController;

/**
 * 动态表单页面
 * https://aisuda.bce.baidu.com/amis/zh-CN/components/service#动态渲染表单项
 */
abstract class DynamicFormPage extends DynamicPage
{
    /**
     * schema 中的 data
     * @return array
     */
    protected function schemaData(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function buildPageSchema($schema): array
    {
        $schema = parent::buildPageSchema($schema);
        if ($data = $this->schemaData()) {
            $schema['data'] = $data;
        }

        return $schema;
    }

    /**
     * 提交表单的 api schema 的 data
     * @return array
     */
    protected function submitApiSchemaData(): array
    {
        return [
            '&' => '$$', // 上级表单的全部数据
        ];
    }

    /**
     * 获取提交表单的 api schema
     * @return array
     */
    public function getSubmitApiSchema(array $data = []): array
    {
        return [
            'method' => 'post',
            'url' => route('amis-dynamic-service.handle'),
            'data' => array_merge(
                [
                    AmisDynamicPageController::$requestServiceKey => static::class,
                ],
                $this->submitApiSchemaData(),
            ),
        ];
    }

    /**
     * 处理表单提交操作
     * @param Request $request
     * @return array|Response|mixed
     */
    abstract public function handleSubmit(Request $request);
}
