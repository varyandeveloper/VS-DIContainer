<?php

namespace VS\DIContainer\Configuration;

/**
 * Interface ConfigurationInterface
 * @package VS\DIContainer\Configuration
 */
interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param array $config
     * @return mixed
     */
    public function setConfig(array $config);

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @return mixed
     */
    public function getByKey(string $key);
}