<?php

namespace VS\DIContainer;

/**
 * Interface DIContainerInterface
 * @package VS\DIContainer\DIContainer
 */
interface DIContainerInterface
{
    /**
     * @param string $className
     * @param null|string $factoryClass
     * @param null|string $alias
     * @return DIContainer
     */
    public function register(string $className, ?string $factoryClass = null, ?string $alias = null): DIContainer;

    /**
     * @param string $className
     * @param string $factoryClass
     * @return DIContainer
     */
    public function registerFactory(string $className, string $factoryClass): DIContainer;

    /**
     * @param string $className
     * @param string $alias
     * @return DIContainer
     */
    public function registerAlias(string $className, string $alias): DIContainer;

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