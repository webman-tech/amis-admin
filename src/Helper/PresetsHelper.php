<?php

namespace WebmanTech\AmisAdmin\Helper;

use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Helper\DTO\PresetItem;
use WebmanTech\AmisAdmin\Repository\AbsRepository;

class PresetsHelper
{
    /**
     * @var array<string, PresetItem>
     */
    protected array $presets = [];
    protected array $sceneKeys = [];

    /**
     * 添加预设
     * @param array $presets
     * @return $this
     */
    public function withPresets(array $presets): static
    {
        foreach ($presets as $attribute => $define) {
            if (!$define instanceof PresetItem) {
                throw new \InvalidArgumentException('presets item must be instance of PresetItem');
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
    public function withSceneKeys(array $data): static
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
    public function withDefaultSceneKeys(array $keys): static
    {
        return $this->withSceneKeys([
            PresetItem::SCENE_DEFAULT => $keys,
        ]);
    }

    /**
     * 设置 CRUD 场景对应的字段
     * @param array $keys
     * @return $this
     */
    public function withCrudSceneKeys(array $keys): static
    {
        return $this->withSceneKeys([
            AbsRepository::SCENE_LIST => $keys,
            AbsRepository::SCENE_CREATE => $keys,
            AbsRepository::SCENE_UPDATE => $keys,
            AbsRepository::SCENE_DETAIL => $keys,
        ]);
    }

    protected string $scene = PresetItem::SCENE_DEFAULT;

    /**
     * @return $this
     */
    public function withScene(?string $scene = null): static
    {
        $this->scene = $scene ?? PresetItem::SCENE_DEFAULT;
        return $this;
    }

    /**
     * 获取字段对应的 label
     * @param array|null $keys
     * @return array<string, string>
     */
    public function pickLabel(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getLabel();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 labelRemark
     * @param array|null $keys
     * @return array<string, string>
     */
    public function pickLabelRemark(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getLabelRemark();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 description
     * @param array|null $keys
     * @return array<string, string>
     */
    public function pickDescription(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getDescription();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 filter
     * @param array|null $keys
     * @return array<string, \Closure>
     */
    public function pickFilter(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getFilter();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 grid
     * @param array|null $keys
     * @return GridColumn[]
     */
    public function pickGrid(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getGrid();
            if ($value === null) {
                continue;
            }
            $data = array_merge($data, $value);
        }
        return $data;
    }

    /**
     * 获取字段对应的 form
     * @param array|null $keys
     * @return FormField[]
     */
    public function pickForm(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getForm();
            if ($value === null) {
                continue;
            }
            $data = array_merge($data, $value);
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
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getRules();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 ruleMessages
     * @param array|null $keys
     * @return array
     */
    public function pickRuleMessages(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getRuleMessages();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 ruleCustomAttributes
     * @param array|null $keys
     * @return array
     */
    public function pickRuleCustomAttributes(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getRuleCustomAttribute();
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取字段对应的 detail
     * @param array|null $keys
     * @return DetailAttribute[]
     */
    public function pickDetail(?array $keys = null): array
    {
        $data = [];
        foreach ($this->getPresetItems($keys) as $key => $item) {
            $value = $item->withKey($key)->withScene($this->scene)->getDetail();
            if ($value === null) {
                continue;
            }
            $data = array_merge($data, $value);
        }
        return $data;
    }

    /**
     * 获取 presetItems
     * @param array|null $keys
     * @return array<string, PresetItem>
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
}
