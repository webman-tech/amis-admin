<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Amis\Traits\ComponentCommonFn;

/**
 * 详情的一个字段
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/static
 *
 * @method $this name(string|null $value)
 * @method $this label(string|null $value)
 * @method $this value(string|null $value)
 * @method $this type(string|null $value)
 * @method $this badge(array|null $schema)
 * @method $this labelRemark(string|null $value)
 * @method $this description(string|null $value)
 * @method $this hidden(bool $is = true)
 * @method $this hiddenOn(string $expression)
 * @method $this visible(bool $is = true)
 * @method $this visibleOn(string $expression)
 *
 * @method $this typeCode(array $schema = [])
 * @method $this typeColor(array $schema = [])
 * @method $this typeDate(array $schema = [])
 * @method $this typeDatetime(array $schema = [])
 * @method $this typeHtml(array $schema = [])
 * @method $this typeImage(array $schema = [])
 * @method $this typeImages(array $schema = [])
 * @method $this typeJson(array $schema = [])
 * @method $this typeLink(array $schema = [])
 * @method $this typeMapping(array $schema)
 * @method $this typeProgress(array $schema = [])
 * @method $this typeQrcode(array $schema = [])
 * @method $this typeBarcode(array $schema = [])
 * @method $this typeTag(array $schema = [])
 * @method $this typeAvatar(array $schema = [])
 */
class DetailAttribute extends Component
{
    use ComponentCommonFn;

    protected array $schema = [
        'type' => 'static',
        'name' => '',
    ];

    protected array $defaultValue = [
        'hidden' => true,
        'visible' => true,
    ];

    /**
     * 复制
     * @param null|string $content
     * @return $this
     */
    public function copyable(string $content = null)
    {
        $this->schema['copyable'] = [
            'content' => $content ?? "\${$this->schema['name']}",
        ];
        return $this;
    }

    /**
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        if (strlen($name) > 4 && str_starts_with($name, 'type')) {
            $this->schema['type'] = 'static-' . lcfirst(substr($name, 4));
            $this->schema($arguments[0] ?? []);
        } else {
            $this->callToSetSchema($name, $arguments);
        }
        return $this;
    }

    public function toArray(): array
    {
        $this->solveType();

        return parent::toArray();
    }

    protected function solveType(): void
    {
        $type = $this->schema['type'];
        if ($type === 'static-mapping') {
            $this->solveMappingMap();
        }
    }
}
