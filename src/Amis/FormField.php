<?php

namespace WebmanTech\AmisAdmin\Amis;

use WebmanTech\AmisAdmin\Amis\Traits\ComponentCommonFn;

/**
 * 表单的一个字段
 *
 * @method $this type(string|null $value)
 * @method $this name(string|null $value)
 * @method $this label(string|false|null $value)
 * @method $this value(string|null $value)
 * @method $this size(string|null $value) 'xs' | 'sm' | 'md' | 'lg' | 'full'
 * @method $this placeholder(string|null $value)
 * @method $this labelRemark(string|array|null $value)
 * @method $this description(string|null $value)
 * @method $this disabled(bool $is = true)
 * @method $this disabledOn(string $expression)
 * @method $this hidden(bool $is = true)
 * @method $this hiddenOn(string $expression)
 * @method $this visible(bool $is = true)
 * @method $this visibleOn(string $expression)
 * @method $this required(bool $is = true)
 * @method $this requiredOn(string $expression)
 * @method $this static (bool $is = true)
 * @method $this validations(array $rules)
 * @method $this validationErrors(array $messages)
 * @method $this validateOnChange(bool $is = true)
 *
 * @method $this typeCheckbox(array $schema = [])
 * @method $this typeCheckboxs(array $schema = [])
 * @method $this typeInputCity(array $schema = [])
 * @method $this typeInputColor(array $schema = [])
 * @method $this typeInputDate(array $schema = [])
 * @method $this typeInputDateTime(array $schema = [])
 * @method $this typeInputMonth(array $schema = [])
 * @method $this typeInputDateRange(array $schema = [])
 * @method $this typeInputDatetimeRange(array $schema = [])
 * @method $this typeInputMonthRange(array $schema = [])
 * @method $this typeInputKv(array $schema = [])
 * @method $this typeInputFormula(array $schema = [])
 * @method $this typeEditor(array $schema = [])
 * @method $this typeFieldSet(array $schema = [])
 * @method $this typeInputExcel(array $schema = [])
 * @method $this typeInputFile(array $schema = [])
 * @method $this typeFormula(array $schema = [])
 * @method $this typeGroup(array $schema = [])
 * @method $this typeHidden(array $schema = [])
 * @method $this typeInputImage(array $schema = [])
 * @method $this typeInputGroup(array $schema = [])
 * @method $this typeListSelect(array $schema = [])
 * @method $this typeLocationPicker(array $schema = [])
 * @method $this typeUuid(array $schema = [])
 * @method $this typeMatrixCheckboxes(array $schema = [])
 * @method $this typeNestedSelect(array $schema = [])
 * @method $this typeInputNumber(array $schema = [])
 * @method $this typeInputPassword(array $schema = [])
 * @method $this typePicker(array $schema = [])
 * @method $this typeInputQuarter(array $schema = [])
 * @method $this typeInputQuarterRange(array $schema = [])
 * @method $this typeRadios(array $schema = [])
 * @method $this typeChartRadios(array $schema = [])
 * @method $this typeInputRating(array $schema = [])
 * @method $this typeInputRange(array $schema = [])
 * @method $this typeInputRepeat(array $schema = [])
 * @method $this typeInputRichText(array $schema = [])
 * @method $this typeSelect(array $schema = [])
 * @method $this typeInputSubForm(array $schema = [])
 * @method $this typeSwitch(array $schema = [])
 * @method $this typeStatic(array $schema = [])
 * @method $this typeInputTable(array $schema = [])
 * @method $this typeInputTag(array $schema = [])
 * @method $this typeInputText(array $schema = [])
 * @method $this typeTextarea(array $schema = [])
 * @method $this typeInputTime(array $schema = [])
 * @method $this typeInputTimeRange(array $schema = [])
 * @method $this typeTransfer(array $schema = [])
 * @method $this typeTransferPicker(array $schema = [])
 * @method $this typeTabsTransfer(array $schema = [])
 * @method $this typeTabsTransferPicker(array $schema = [])
 * @method $this typeInputTree(array $schema = [])
 * @method $this typeTreeSelect(array $schema = [])
 * @method $this typeInputYear(array $schema = [])
 * @method $this typeInputYearRange(array $schema = [])
 * @method $this typeJsonSchema(array $schema = [])
 * @method $this typeJsonSchemaEditor(array $schema = [])
 */
class FormField extends Component
{
    use ComponentCommonFn;

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
        'static' => true,
        'validateOnChange' => true,
    ];

    /**
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        if (strlen($name) > 4 && str_starts_with($name, 'type')) {
            /** @phpstan-ignore-next-line */
            $this->schema['type'] = strtolower((string) preg_replace('/(?<=[a-z])([A-Z])/', '-$1', lcfirst(substr($name, 4))));
            $this->schema($arguments[0] ?? []);
        } else {
            $this->callToSetSchema($name, $arguments);
        }
        return $this;
    }
}
