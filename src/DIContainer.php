<?php

namespace VS\DIContainer;

use VS\DIContainer\Injector\{
    Injector, InjectorException
};

/**
 * Class DIContainer
 * @package VS\DIContainer\DIContainer
 */
class DIContainer implements DIContainerInterface
{
    /**
     * @var array
     */
    protected static $classToFactory    = [];
    /**
     * @var array
     */
    protected static $classToAlias      = [];
    /**
     * @var array
     */
    protected static $singletonState    = [];

    /**
     * @param string $className
     * @param null|string $factoryClass
     * @param null|string $alias
     * @return DIContainer
     */
    public function register(string $className, ?string $factoryClass = null, ?string $alias = null): DIContainer
    {
        if (null === $factoryClass && null === $alias) {
            trigger_error(
                'Using register method without factory and alias dose not have an effect',
                E_USER_WARNING
            );

            return $this;
        }

        if (null !== $factoryClass) {
            $this->registerFactory($className, $factoryClass);
        }

        if (null !== $alias) {
            $this->registerAlias($className, $alias);
        }

        return $this;
    }

    /**
     * @param string $className
     * @param string $factoryClass
     * @return DIContainer
     */
    public function registerFactory(string $className, string $factoryClass): DIContainer
    {
        static::$classToFactory[$className] = $factoryClass;
        return $this;
    }

    /**
     * @param string $className
     * @param string $alias
     * @return DIContainer
     */
    public function registerAlias(string $className, string $alias): DIContainer
    {
        static::$classToAlias[$className] = $alias;
        class_alias($className, $alias);
        return $this;
    }

    /**
     * @param string $className
     * @param mixed ...$params
     * @return object
     * @throws InjectorException
     */
    public function get(string $className, ...$params): object
    {
        if (!empty(static::$classToAlias[$className])) {
            $className = static::$classToAlias[$className];
        }

        if (!empty(static::$classToFactory[$className])) {
            $className = static::$classToFactory[$className];
            $this->validateClass($className);
            $object = new $className;
            return $object($this, $params);
        } else {
            $this->validateClass($className);
            return Injector::injectClass($className, ...$params);
        }
    }

    /**
     * @param string $className
     * @return object
     * @throws InjectorException
     */
    public function getSingleton(string $className): object
    {
        if (empty(static::$singletonState[$className])) {
            static::$singletonState[$className] = $this->get($className);
        }

        return static::$singletonState[$className];
    }

    /**
     * @param string $className
     */
    protected function validateClass(string $className)
    {
        if (!class_exists($className)) {
            throw new DIContainerException('Class factory ' . $className . ' dose not exists');
        }
    }
}
