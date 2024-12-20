<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Helper\ArrayHelper;
use WebmanTech\AmisAdmin\Helper\ConfigHelper;
use WebmanTech\AmisAdmin\Helper\ContainerHelper;

/**
 * amis 组件
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/index
 */
class Component
{
    /**
     * 组件的 schema，amis 组件的属性配置
     * @var array<string, mixed>
     */
    protected array $schema = [
        'type' => '',
    ];
    /**
     * 组件配置，用于定义一些非 amis 组件属性的配置
     * @var array<string, mixed>
     */
    protected array $config = [
        /**
         * 组件的默认 schema
         */
        'schema' => [],
    ];

    public function __construct()
    {
        // 从配置中获取到 component 的默认配置
        if ($config = ConfigHelper::getComponentConfig(static::class)) {
            $this->config($config);
            if (isset($config['schema'])) {
                $this->schema($config['schema']);
            }
        }
    }

    /**
     * 创建一个组件
     * @param array|null $schema
     * @return $this
     */
    public static function make(array $schema = null)
    {
        $component = ContainerHelper::make(static::class);
        if ($schema) {
            $component->schema($schema);
        }
        return $component;
    }

    /**
     * 修改 schema
     * @param array $schema
     * @param bool $overwrite
     * @return $this
     */
    public function schema(array $schema, bool $overwrite = false)
    {
        if (!$schema) {
            return $this;
        }

        if ($overwrite) {
            $this->schema = $schema;
        } else {
            $this->schema = $this->merge($this->schema, $schema);
        }
        return $this;
    }

    /**
     * 修改 config
     * @param array $config
     * @param bool $overwrite
     * @return $this
     */
    public function config(array $config, bool $overwrite = false)
    {
        if (!$config) {
            return $this;
        }

        if ($overwrite) {
            $this->config = $config;
        } else {
            $this->config = $this->merge($this->config, $config);
        }
        return $this;
    }

    /**
     * 转成最终数组
     * @return array
     */
    public function toArray(): array
    {
        return $this->deepToArray($this->schema);
    }

    /**
     * 深度转换数组
     * @param array $arr
     * @return array
     */
    protected function deepToArray(array $arr): array
    {
        $newArr = [];
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $item = $this->deepToArray($item);
            } elseif ($item instanceof self) {
                $item = $item->toArray();
            }
            $newArr[$key] = $item;
        }
        return $newArr;
    }

    /**
     * 获取 schema 中的值
     * @param string $schemaKey 支持 a.b.c 的形式
     * @param null $default
     * @return array|mixed
     */
    public function get(string $schemaKey, $default = null)
    {
        return ArrayHelper::get($this->toArray(), $schemaKey, $default);
    }

    /**
     * 合并数组
     * @param ...$arrays
     * @return array
     */
    protected function merge(...$arrays): array
    {
        return ArrayHelper::merge(...$arrays);
    }
}