<?php

namespace Kriss\WebmanAmisAdmin\Amis\Traits;

/**
 * 操作按钮
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 */
trait ActionButtonSupport
{
    /**
     * ajax 请求
     * @param int $index
     * @param string $label
     * @param string $api
     * @param array $schema
     * @return $this
     */
    public function withButtonAjax(int $index, string $label, string $api, array $schema = [])
    {
        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'ajax',
            'api' => $api,
        ], $schema));
    }

    /**
     * 下载请求
     * @param int $index
     * @param string $label
     * @param string $api
     * @param array $schema
     * @return $this
     */
    public function withButtonDownload(int $index, string $label, string $api, array $schema = [])
    {
        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'download',
            'api' => $api,
        ], $schema));
    }

    /**
     * 单页跳转
     * @param int $index
     * @param string $label
     * @param string $link
     * @param array $schema
     * @return $this
     */
    public function withButtonLink(int $index, string $label, string $link, array $schema = [])
    {
        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'link',
            'link' => $link,
        ], $schema));
    }

    /**
     * 直接跳转
     * @param int $index
     * @param string $label
     * @param string $link
     * @param bool $blank
     * @param array $schema
     * @return $this
     */
    public function withButtonUrl(int $index, string $label, string $link, bool $blank = false, array $schema = [])
    {
        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'url',
            'url' => $link,
            'blank' => $blank,
        ], $schema));
    }

    /**
     * 弹框
     * @param int $index
     * @param string $label
     * @param string|array $body
     * @param array $schema
     * @return $this
     */
    public function withButtonDialog(int $index, string $label, $body, array $schema = [])
    {
        if (isset($schema['api'])) {
            $schema['dialog']['body']['api'] = $schema['api'];
            unset($schema['api']);
        }
        if (isset($schema['initApi'])) {
            $schema['dialog']['body']['initApi'] = $schema['initApi'];
            unset($schema['initApi']);
        }

        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'dialog',
            'dialog' => [
                'title' => $label,
                'body' => [
                    'type' => 'form',
                    'body' => $body,
                ],
            ],
        ], $schema));
    }

    /**
     * 抽屉
     * @param int $index
     * @param string $label
     * @param string|array $body
     * @param array $schema
     * @return $this
     */
    public function withButtonDrawer(int $index, string $label, $body, array $schema = [])
    {
        if (isset($schema['api'])) {
            $schema['drawer']['body']['api'] = $schema['api'];
            unset($schema['api']);
        }
        if (isset($schema['initApi'])) {
            $schema['drawer']['body']['initApi'] = $schema['initApi'];
            unset($schema['initApi']);
        }

        return $this->withButton($index, $label, $this->merge([
            'actionType' => 'drawer',
            'drawer' => [
                'title' => $label,
                'body' => [
                    'type' => 'form',
                    'body' => $body,
                ],
            ],
        ], $schema));
    }

    /**
     * @param int $index
     * @param string $label
     * @param array $schema
     * @return $this
     */
    public function withButton(int $index, string $label, array $schema = [])
    {
        $schema['type'] = $schema['type'] ?? 'button';
        $schema['label'] = $schema['label'] ?? $label;
        $this->setActionButton($index, $schema);
        return $this;
    }

    /**
     * 分割线
     * @param int $index
     * @return ActionButtonSupport
     */
    public function withDivider(int $index)
    {
        $schema['type'] = 'divider';
        $this->setActionButton($index, $schema);
        return $this;
    }

    /**
     * 设置 button 到 schema 中
     * @param int $index
     * @param array $schema
     * @return void
     */
    abstract protected function setActionButton(int $index, array $schema): void;
}