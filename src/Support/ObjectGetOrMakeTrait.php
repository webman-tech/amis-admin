<?php

namespace WebmanTech\AmisAdmin\Support;

trait ObjectGetOrMakeTrait
{
    private array $objets = [];

    /**
     * 获取或构建实例
     * @param string $key
     * @param $config
     * @return object|null
     */
    public function getOrMakeObject(string $key, $config): ?object
    {
        if (!isset($this->objets[$key])) {
            if (is_string($config)) {
                $config = new $config();
            } elseif(is_array($config)) {
                $config = new $config['class']($config['construct'] ?? []);
            } elseif (ClosureHelper::isClosure($config)) {
                $config = ClosureHelper::call($config);
            }
            if ($config === null) {
                return $config;
            }
            if (!is_object($config)) {
                throw new \InvalidArgumentException('Not an object');
            }
            $this->objets[$key] = $config;
        }

        return $this->objets[$key];
    }
}
