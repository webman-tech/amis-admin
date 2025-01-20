<?php

namespace WebmanTech\AmisAdmin\Helper;

interface PresetsHelperInterface
{
    /**
     * 添加预设
     * @param array $presets
     * @return $this
     */
    public function withPresets(array $presets);

    /**
     * 默认启用的字段
     * @param array $keys
     * @return $this
     */
    public function withDefaultEnable(array $keys);

    /**
     * 获取字段对应的 label
     * @param array|null $keys
     * @return array
     */
    public function pickLabel(?array $keys = null): array;

    /**
     * 获取字段对应的 filter
     * @param array|null $keys
     * @return array
     */
    public function pickFilter(?array $keys = null): array;

    /**
     * 获取字段对应的 grid
     * @param array|null $keys
     * @return array
     */
    public function pickGrid(?array $keys): array;

    /**
     * 获取字段对应的 form
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickForm(string $scene, ?array $keys = null): array;

    /**
     * 获取字段对应的 rules
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRules(string $scene, ?array $keys = null): array;

    /**
     * 获取字段对应的 messages
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRuleMessages(string $scene, ?array $keys = null): array;

    /**
     * 获取字段对应的 attributes
     * @param string $scene
     * @param array|null $keys
     * @return array
     */
    public function pickRuleCustomAttributes(string $scene, ?array $keys = null): array;

    /**
     * 获取字段对应的 detail
     * @param array|null $keys
     * @return array
     */
    public function pickDetail(?array $keys = null): array;
}