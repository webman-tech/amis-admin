<?php

namespace WebmanTech\AmisAdmin\Amis\Service;

use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis\Component;
use WebmanTech\AmisAdmin\Controller\AmisDynamicPageController;

/**
 * 动态页面
 * https://aisuda.bce.baidu.com/amis/zh-CN/components/service#动态渲染页面
 */
abstract class DynamicPage extends Component
{
    protected array $schema = [
        'type' => 'service',
    ];

    public function toArray(): array
    {
        if (!isset($this->schema['schemaApi'])) {
            $this->schema['schemaApi'] = [
                'method' => 'post',
                'url' => route('amis-dynamic-service.page'),
                'data' => [
                    AmisDynamicPageController::$requestServiceKey => static::class,
                    '&' => '$$',
                ],
            ];
        }

        return parent::toArray();
    }

    /**
     * 页面 schema
     * @return array|Component|mixed
     */
    abstract protected function pageSchema();

    /**
     * 构建页面 schema
     * @param mixed $schema
     * @return array
     */
    protected function buildPageSchema($schema): array
    {
        if (is_array($schema) && isset($schema[0])) {
            // index 数组需要被包在 container 里
            $schema = Component::make(['type' => 'container', 'body' => $schema]);
        }
        if ($schema instanceof Component) {
            $schema = $schema->toArray();
        }
        return $schema;
    }

    /**
     * 响应 page 结构
     * @return Response
     */
    public function responsePage(): Response
    {
        return amis_response(
            $this->buildPageSchema(
                $this->pageSchema()
            )
        );
    }
}