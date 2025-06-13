<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Amis\Traits\ActionButtonSupport;

/**
 * table 的 actions column 列
 */
class GridColumnActions extends GridColumn
{
    use ActionButtonSupport;

    public const INDEX_DETAIL = 10;
    public const INDEX_UPDATE = 20;
    public const INDEX_DELETE = 30;
    public const INDEX_RECOVERY = 40;

    public function __construct()
    {
        $this->config['schema_detail'] = [];
        $this->config['schema_update'] = [];
        $this->config['schema_delete'] = [];
        $this->config['schema_recovery'] = [];
        $this->config['schema'] = [
            'type' => 'operation',
            'label' => trans('操作', [], 'amis-admin'),
            'buttons' => [],
        ];

        parent::__construct();
    }

    /**
     * 详情
     * @param array $detailAttributes
     * @param string|null $initApi
     * @param string $can
     * @return $this
     */
    public function withDetail(array $detailAttributes, ?string $initApi = null, string $can = '1==1')
    {
        $label = $this->config['schema_detail']['label'] ?? trans('详情', [], 'amis-admin');
        return $this->withButtonDialog(static::INDEX_DETAIL, $label, $detailAttributes, $this->merge([
            'initApi' => $initApi,
            'visibleOn' => $can,
            'dialog' => [
                'closeOnOutside' => true,
                'actions' => [
                    ['type' => 'button', 'label' => trans('取消', [], 'amis-admin'), 'actionType' => 'cancel'],
                ],
            ],
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
    public function withUpdate(array $formFields, string $api, ?string $initApi = null, string $can = '1==1')
    {
        $label = $this->config['schema_update']['label'] ?? trans('修改', [], 'amis-admin');
        return $this->withButtonDialog(static::INDEX_UPDATE, $label, $formFields, $this->merge([
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
        $label = $this->config['schema_delete']['label'] ?? trans('删除', [], 'amis-admin');
        return $this->withButtonAjax(static::INDEX_DELETE, $label, $api, $this->merge([
            'level' => 'danger',
            'confirmText' => trans('确定要%operate%？', ['%operate%' => $label], 'amis-admin'),
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
        $label = $this->config['schema_recovery']['label'] ?? trans('恢复', [], 'amis-admin');
        return $this->withButtonAjax(static::INDEX_RECOVERY, $label, $api, $this->merge([
            'level' => 'warning',
            'confirmText' => trans('确定要%operate%？', ['%operate%' => $label], 'amis-admin'),
            'visibleOn' => $can,
        ], $this->config['schema_recovery']));
    }

    /**
     * @inheritdoc
     */
    protected function setActionButton(int $index, array $schema): void
    {
        $this->schema['buttons'][$index] = $schema;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        ksort($this->schema['buttons']);
        $this->schema['buttons'] = array_filter(array_values($this->schema['buttons']));
        return parent::toArray();
    }
}
