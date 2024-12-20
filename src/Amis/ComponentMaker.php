<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Helper\ConfigHelper;

class ComponentMaker
{
    private static ?array $formTypeMethods = null;

    /**
     * 获取 form 类型的方法名
     * @return array
     */
    private static function getFormTypeMethods(): array
    {
        if (static::$formTypeMethods === null) {
            static::$formTypeMethods = require_once __DIR__ . '/resource/form_types.php';
        }
        return static::$formTypeMethods;
    }

    /**
     * 获取默认的全部的组件类映射
     * @return array<string, class-string<Component>>
     */
    protected static function getDefaultClassMap(): array
    {
        return [
            'typePage' => Page::class,
        ];
    }

    /**
     * 根据 name 获取默认的组件类
     * @param string $name
     * @return string
     */
    public static function getDefaultClassByName(string $name): string
    {
        if ($value = static::getDefaultClassMap()[$name] ?? null) {
            return $value;
        }
        if (in_array($name, static::getFormTypeMethods(), true)) {
            return FormField::class;
        }
        return Component::class;
    }

    public function __call(string $name, array $arguments): Component
    {
        // 根据类型获取全局配置
        $config = ConfigHelper::getComponentConfig($name);
        // 转化为 amis 的 type: typeAbcXyz -> abc-xyz
        $type = ltrim(strtolower(preg_replace('/([A-Z])/', '-$1', substr($name, 4))), '-');
        // 获取组件类
        $componentClass = $config['class'] ?? static::getDefaultClassByName($name);
        if (!is_a($componentClass, Component::class, true)) {
            throw new \RuntimeException("The class $componentClass must be a subclass of " . Component::class);
        }
        // 创建组件
        $component = $componentClass::make([
            'type' => $type,
        ]);
        // 全局的配置
        $component->config($config);
        if (isset($config['schema']) && is_array($config['schema'])) {
            $component->schema($config['schema']);
        }
        // 本次的配置
        if (($schema = $arguments[0] ?? null) && is_array($schema)) {
            $component->schema($schema);
        }

        return $component;
    }
}