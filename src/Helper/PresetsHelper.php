<?php

namespace WebmanTech\AmisAdmin\Helper;

use WebmanTech\AmisAdmin\Helper\DTO\PresetItemDTO;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class PresetsHelper
{
    private const SCENE_DEFAULT = 'default';

    protected bool $defaultNoEdit = false;
    /**
     * @var array<string, PresetItemDTO>
     */
    protected array $presets = [];
    protected array $sceneKeys = [];

    /**
     * 默认不启用编辑（只读）
     * @return $this
     */
    public function withDefaultNoEdit()
    {
        $this->defaultNoEdit = true;
        return $this;
    }

    /**
     * 添加预设
     * @param array $presets
     * @return $this
     */
    public function withPresets(array $presets)
    {
        foreach ($presets as $attribute => $define) {
            if (!$define instanceof PresetItemDTO) {
                $define = new PresetItemDTO($attribute, $define, [
                    'defaultCanEdit' => !$this->defaultNoEdit,
                ]);
            }
            $this->presets[$attribute] = $define;
        }
        return $this;
    }

    /**
     * 设置场景对应的字段
     * @param array $data
     * @return $this
     */
    public function withSceneKeys(array $data)
    {
        foreach ($data as $scene => $keys) {
            $this->sceneKeys[$scene] = $keys;
        }
        return $this;
    }

    /**
     * 设置默认场景对应的字段
     * @param array $keys
     * @return $this
     */
    public function withDefaultSceneKeys(array $keys)
    {
        return $this->withSceneKeys([
            self::SCENE_DEFAULT => $keys,
        ]);
    }

    /**
     * 设置 CRUD 场景对应的字段
     * @param array $keys
     * @return $this
     */
    public function withCrudSceneKeys(array $keys)
    {
        return $this->withSceneKeys([
            AbsRepository::SCENE_LIST => $keys,
            AbsRepository::SCENE_CREATE => $keys,
            AbsRepository::SCENE_UPDATE => $keys,
            AbsRepository::SCENE_DETAIL => $keys,
        ]);
    }

    protected string $scene = self::SCENE_DEFAULT;

    public function withScene(?string $scene = null)
    {
        $this->scene = $scene ?? self::SCENE_DEFAULT;
        return $this;
    }

    /**
     * 获取字段对应的 label
     * @param array|null $keys
     * @return array
     */
    public function pickLabel(?array $keys = null): array
    {
        return $this->pickColumn('label', $keys, true);
    }

    /**
     * 获取字段对应的 labelRemark
     * @param array|null $keys
     * @return array
     */
    public function pickLabelRemark(?array $keys = null): array
    {
        return $this->pickColumn('labelRemark', $keys, true);
    }

    /**
     * 获取字段对应的 description
     * @param array|null $keys
     * @return array
     */
    public function pickDescription(?array $keys = null): array
    {
        return $this->pickColumn('description', $keys, true);
    }

    /**
     * 获取字段对应的 filter
     * @param array|null $keys
     * @return array
     */
    public function pickFilter(?array $keys = null): array
    {
        return $this->pickColumn('filter', $keys, true);
    }

    /**
     * 获取字段对应的 grid
     * @param array|null $keys
     * @return array
     */
    public function pickGrid(?array $keys = null): array
    {
        return $this->pickColumn('grid', $keys);
    }

    /**
     * 获取字段对应的 form
     * @param array|null $keys
     * @return array
     */
    public function pickForm(?array $keys = null): array
    {
        $items = $this->pickColumn('form', $keys);
        // 允许同时展示多个 FormItem
        $data = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $data = array_merge($data, $item);
            } else {
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 rules
     * @param array|null $keys
     * @return array
     */
    public function pickRules(?array $keys = null): array
    {
        return $this->pickColumn('rule', $keys, true);
    }

    /**
     * 获取字段对应的 ruleMessages
     * @param array|null $keys
     * @return array
     */
    public function pickRuleMessages(?array $keys = null): array
    {
        return $this->pickColumn('ruleMessages', $keys, true);
    }

    /**
     * 获取字段对应的 ruleCustomAttributes
     * @param array|null $keys
     * @return array
     */
    public function pickRuleCustomAttributes(?array $keys = null): array
    {
        return $this->pickColumn('ruleCustomAttribute', $keys, true);
    }

    /**
     * 获取字段对应的 detail
     * @param array|null $keys
     * @return array
     */
    public function pickDetail(?array $keys = null): array
    {
        return $this->pickColumn('detail', $keys);
    }

    /**
     * 获取 presetItems
     * @param array|null $keys
     * @return array<string, PresetItemDTO>
     */
    protected function getPresetItems(?array $keys = null): array
    {
        $keys = $this->getKeys($keys);

        $data = [];
        foreach ($keys as $key) {
            if (!isset($this->presets[$key])) {
                continue;
            }
            $data[$key] = $this->presets[$key];
        }

        return $data;
    }

    /**
     * 获取场景对应的 keys
     * @param array|null $keys
     * @return array
     */
    private function getKeys(?array $keys = null): array
    {
        if ($keys === null) {
            $keys = $this->sceneKeys[$this->scene] ?? array_keys($this->presets);
        }
        return $keys;
    }

    /**
     * 提取某个类型的数据
     * @param string $type
     * @param array|null $keys
     * @param bool $keepKey
     * @return array
     */
    protected function pickColumn(string $type, ?array $keys = null, bool $keepKey = false): array
    {
        $items = $this->getPresetItems($keys);
        $data = [];
        foreach ($items as $key => $item) {
            $value = $item->withScene($this->scene)->{$type};
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        if (!$keepKey) {
            $data = array_values($data);
        }
        return $data;
    }
}
