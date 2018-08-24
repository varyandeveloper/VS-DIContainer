<?php

namespace VS\DIContainer\Configuration;

/**
 * Trait ConfigurationTrait
 * @package VS\DIContainer\Configuration
 */
trait ConfigurationTrait
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return ConfigurationInterface
     */
    public function setConfig(array $config): ConfigurationInterface
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getByKey(string $key)
    {
        if (!$this->has($key)) {
            throw new ConfigurationKeyNotFoundException(sprintf(
                'Configuration key %s dose not exists',
                $key
            ));
        }

        return $this->config[$key];
    }
}