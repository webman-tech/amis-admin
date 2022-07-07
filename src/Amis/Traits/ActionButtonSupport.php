<?php

namespace Kriss\WebmanAmisAdmin\Amis\Traits;

trait ActionButtonSupport
{
    /**
     * dialog button
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
     * ajax button
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
     * @param int $index
     * @param string $label
     * @param array $schema
     * @return $this
     */
    public function withButton(int $index, string $label, array $schema)
    {
        $schema['type'] = $schema['type'] ?? 'button';
        $schema['label'] = $schema['label'] ?? $label;
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