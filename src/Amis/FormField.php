<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Facades\AmisFacade;

/**
 * 表单的一个字段
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/formitem
 *
 * @method $this type(string $value)
 * @method $this name(string $value)
 * @method $this label(string|false $value)
 * @method $this value(string $value)
 * @method $this size(string $value) 'xs' | 'sm' | 'md' | 'lg' | 'full'
 * @method $this placeholder(string $value)
 * @method $this labelRemark(string|array $value)
 * @method $this description(string $value)
 * @method $this disabled(bool $is = true)
 * @method $this disabledOn(string $expression)
 * @method $this hidden(bool $is = true)
 * @method $this hiddenOn(string $expression)
 * @method $this visible(bool $is = true)
 * @method $this visibleOn(string $expression)
 * @method $this required(bool $is = true)
 * @method $this requiredOn(string $expression)
 * @method $this validations(array $rules)
 * @method $this validationErrors(array $messages)
 * @method $this validateOnChange(bool $is = true)
 */
class FormField extends Component
{
    protected array $schema = [
        'type' => 'input-text',
        'name' => '',
        'clearable' => true,
    ];

    protected array $defaultValue = [
        'disabled' => true,
        'hidden' => true,
        'visible' => true,
        'required' => true,
        'validateOnChange' => true,
    ];

    public function __call($name, $arguments)
    {
        if (strlen($name) > 4 && strpos($name, 'type') === 0) {
            // 不再建议使用 typeXxx，使用 Amis::typeXxx 代替
            return AmisFacade::__callStatic($name, $arguments);
        }

        $value = $arguments[0] ?? null;
        if ($value === null) {
            $value = $this->defaultValue[$name] ?? null;
        }
        $this->schema[$name] = $value;
        return $this;
    }
}