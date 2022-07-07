<?php

namespace Kriss\WebmanAmisAdmin\Amis;

/**
 * table 的 actions column 列
 */
class GridColumnActions extends GridColumn
{
    public const INDEX_DETAIL = 10;
    public const INDEX_UPDATE = 20;
    public const INDEX_DELETE = 30;
    public const INDEX_RECOVERY = 40;

    public function __construct()
    {
        $this->schema([
            'type' => 'operation',
            'label' => '操作',
            'buttons' => [],
        ]);
        $this->config['schema_detail'] = [];
        $this->config['schema_update'] = [];
        $this->config['schema_delete'] = [];
        $this->config['schema_recovery'] = [];

        parent::__construct();
    }

    /**
     * 详情
     * @param array $detailAttributes
     * @param string|null $initApi
     * @param string $can
     * @return $this
     */
    public function withDetail(array $detailAttributes, string $initApi = null, string $can = '1==1')
    {
        return $this->withDialogButton(static::INDEX_DETAIL, '详情', $detailAttributes, $this->merge([
            'initApi' => $initApi,
            'visibleOn' => $can,
        ], $this->config['schema_detail']));
    }

    /**
     * 修改
     * @param array $formFields
     * @param string $api
     * @param string|null $initApi
     * @param string $can
     * @return $this
     */
    public function withUpdate(array $formFields, string $api, string $initApi = null, string $can = '1==1')
    {
        return $this->withDialogButton(static::INDEX_UPDATE, '修改', $formFields, $this->merge([
            'initApi' => $initApi,
            'api' => $api,
            'level' => 'primary',
            'visibleOn' => $can,
        ], $this->config['schema_update']));
    }

    /**
     * 删除
     * @param string $api
     * @param string $can
     * @return $this
     */
    public function withDelete(string $api, string $can = '1==1')
    {
        return $this->withAjaxButton(static::INDEX_DELETE, '删除', $api, $this->merge([
            'level' => 'danger',
            'confirmText' => '确定要删除？',
            'visibleOn' => $can,
        ], $this->config['schema_delete']));
    }

    /**
     * 恢复
     * @param string $api
     * @param string $can
     * @return $this
     */
    public function withRecovery(string $api, string $can = '1==1')
    {
        return $this->withAjaxButton(static::INDEX_RECOVERY, '恢复', $api, $this->merge([
            'level' => 'warning',
            'confirmText' => '确定要恢复？',
            'visibleOn' => $can,
        ], $this->config['schema_recovery']));
    }

    /**
     * dialog button
     * @param int $index
     * @param string $label
     * @param string|array $body
     * @param array $schema
     * @return $this
     */
    public function withDialogButton(int $index, string $label, $body, array $schema = [])
    {
        if (isset($schema['api'])) {
            $schema['dialog']['body']['api'] = $schema['api'];
            unset($schema['api']);
        }
        if (isset($schema['initApi'])) {
            $schema['dialog']['body']['initApi'] = $schema['initApi'];
            unset($schema['initApi']);
        }

        return $this->withButton($index, $this->merge([
            'type' => 'button',
            'label' => $label,
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
     * ajax button
     * @param int $index
     * @param string $label
     * @param string $api
     * @param array $schema
     * @return $this
     */
    public function withAjaxButton(int $index, string $label, string $api, array $schema = [])
    {
        return $this->withButton($index, $this->merge([
            'type' => 'button',
            'label' => $label,
            'actionType' => 'ajax',
            'api' => $api,
        ], $schema));
    }

    /**
     * @param int $index
     * @param $config
     * @return $this
     */
    public function withButton(int $index, $config)
    {
        $this->schema['buttons'][$index] = $config;
        return $this;
    }

    public function toArray(): array
    {
        ksort($this->schema['buttons']);
        $this->schema['buttons'] = array_filter(array_values($this->schema['buttons']));
        return parent::toArray();
    }
}