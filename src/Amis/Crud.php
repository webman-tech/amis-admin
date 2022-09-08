<?php

namespace Kriss\WebmanAmisAdmin\Amis;

use Kriss\WebmanAmisAdmin\Amis\Traits\ActionButtonSupport;

/**
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/crud
 */
class Crud extends Component
{
    use ActionButtonSupport;

    public const INDEX_RELOAD = 10;
    public const INDEX_BULK_ACTIONS = 20;
    public const INDEX_CREATE = 30;
    public const INDEX_COLUMNS_TOGGLE = 90;
    public const INDEX_SWITCH_PER_PAGE = 10;
    public const INDEX_PAGINATION = 20;

    public function __construct()
    {
        $this->schema = [
            'type' => 'crud',
            'syncLocation' => false,
            'autoGenerateFilter' => true,
            'alwaysShowPagination' => true,
            'headerToolbar' => [
                static::INDEX_RELOAD => 'reload',
                static::INDEX_BULK_ACTIONS => 'bulkActions',
                static::INDEX_COLUMNS_TOGGLE => [
                    'type' => 'columns-toggler',
                    'align' => 'right',
                    'draggable' => true,
                    'icon' => 'fas fa-cog',
                ],
            ],
            'footerToolbar' => [
                static::INDEX_SWITCH_PER_PAGE => 'switch-per-page',
                static::INDEX_PAGINATION => 'pagination',
            ],
            'columns' => [],
        ];
        $this->config['schema_create'] = [];

        parent::__construct();
    }

    public function withColumns(array $columns)
    {
        $quickEditEnable = isset($this->schema['quickSaveItemApi']);
        foreach ($columns as $index => &$column) {
            if ($column instanceof GridColumnActions) {
                // 去除操作栏为空 buttons 的
                if (count($column->get('buttons', [])) <= 0) {
                    unset($columns[$index]);
                    continue;
                }
            }
            if ($column instanceof Component) {
                $column = $column->toArray();
            }
            if ($quickEditEnable) {
                // 处理 quickEdit 为 true 时自动添加 saveImmediately = true
                if (isset($column['quickEdit']) && $column['quickEdit'] !== false) {
                    if (!is_array($column['quickEdit'])) {
                        $column['quickEdit'] = [];
                    }
                    $column['quickEdit']['saveImmediately'] = true;
                }
            }
        }
        unset($column);

        $this->schema['columns'] = $columns;
        return $this;
    }

    /**
     * 新增按钮
     * @param string $api
     * @param array $form
     * @param string $can
     * @return Crud
     */
    public function withCreate(string $api, array $form, string $can = '1==1')
    {
        $label = $this->config['schema_create']['label'] ?? '新增';
        return $this->withButtonDialog(static::INDEX_CREATE, $label, $form, $this->merge([
            'api' => $api,
            'level' => 'primary',
            'visibleOn' => $can,
        ], $this->config['schema_create']));
    }

    /**
     * @param int $index
     * @param $schema
     * @return $this
     */
    public function withHeaderToolbar(int $index, $schema)
    {
        $this->schema['headerToolbar'][$index] = $schema;
        return $this;
    }

    /**
     * @param int $index
     * @param $schema
     * @return $this
     */
    public function withFooterToolbar(int $index, $schema)
    {
        $this->schema['footerToolbar'][$index] = $schema;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function setActionButton(int $index, array $schema): void
    {
        $this->schema['headerToolbar'][$index] = $schema;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        ksort($this->schema['headerToolbar']);
        $this->schema['headerToolbar'] = array_filter(array_values($this->schema['headerToolbar']));
        ksort($this->schema['footerToolbar']);
        $this->schema['footerToolbar'] = array_filter(array_values($this->schema['footerToolbar']));
        return parent::toArray();
    }
}