<?php

namespace WebmanTech\AmisAdmin\Amis;

/**
 * 详情的一个字段
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/static
 *
 * @method $this name(string $value)
 * @method $this label(string $value)
 * @method $this value(string $value)
 * @method $this type(string $value)
 * @method $this badge(array $schema)
 * @method $this labelRemark(string $value)
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
            'content' => $content === null ? "\${$this->schema['name']}" : $content,
        ];
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (strlen($name) > 4 && strpos($name, 'type') === 0) {
            $this->schema['type'] = 'static-' . lcfirst(substr($name, 4));
            $this->schema($arguments[0] ?? []);
        } else {
            $value = $arguments[0] ?? null;
            if ($value === null) {
                $value = $this->defaultValue[$name] ?? null;
            }
            $this->schema[$name] = $value;
        }
        return $this;
    }
}