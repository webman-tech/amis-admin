<?php

namespace WebmanTech\AmisAdmin\Amis\ActionPage;

use Webman\Http\Response;
use WebmanTech\AmisAdmin\Amis\Component;

abstract class BaseActionPage
{
    protected array $exts = [];

    public function addExt(string $key, \Closure $closure)
    {
        $this->exts[$key][] = $closure;
        return $this;
    }

    /**
     * 响应页面
     * @return Response
     */
    public function responsePage(): Response
    {
        return amis_response($this->pageSchema());
    }

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
     * 页面 schema
     * @return array|Component|mixed
     */
    abstract protected function pageSchema();
}