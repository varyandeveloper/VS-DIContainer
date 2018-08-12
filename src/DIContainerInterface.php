<?php

namespace VS\DIContainer;

use VS\DIContainer\Configuration\ConfigurationInterface;

/**
 * Interface DIContainerInterface
 * @package VS\DIContainer\DIContainer
 */
interface DIContainerInterface
{
    /**
     * @param string $className
     * @param ConfigurationInterface $config
     * @return mixed
     */
    public function registerConfig(string $className, ConfigurationInterface $config);

    /**
     * @param string $className
     * @return ConfigurationInterface
     */
    public function getConfig(string $className): ConfigurationInterface;

    /**
     * @param string $className
     * @param null|string $factoryClass
     * @param null|string $alias
     * @return DIContainer
     */
    public function register(string $className, ?string $factoryClass = null, ?string $alias = null);

    /**
     * @param string $className
     * @param string $factoryClass
     * @return DIContainer
     */
    public function registerFactory(string $className, string $factoryClass);

    /**
     * @param string $className
     * @param string $alias
     * @return DIContainer
     */
    public function registerAlias(string $className, string $alias);

    /**
     * @param string $class
     * @param mixed ...$params
     * @return object
     */
    public function get(string $class, ...$params): object;

    /**
     * @param string $class
     * @return bool
     */
    public function has(string $class): bool;

    /**
     * @param string $className
     * @return object
     */
    public function getSingleton(string $className): object;
}