<?php

namespace VS\DIContainer;

use VS\DIContainer\Configuration\ConfigurationInterface;
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
     * @var array
     */
    protected static $factoryToConfig   = [];

    /**
     * @param string $className
     * @param ConfigurationInterface $config
     * @return DIContainerInterface
     */
    public function registerConfig(string $className, ConfigurationInterface $config): DIContainerInterface
    {
        self::$factoryToConfig[$className] = $config;
        return $this;
    }

    /**
     * @param string $className
     * @return ConfigurationInterface
     */
    public function getConfig(string $className): ConfigurationInterface
    {
        return self::$factoryToConfig[$className];
    }

    /**
     * @param string $className
     * @param null|string $factoryClass
     * @param null|string $alias
     * @return DIContainer
     */
    public function register(string $className, ?string $factoryClass = null, ?string $alias = null): DIContainerInterface
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
    public function registerFactory(string $className, string $factoryClass): DIContainerInterface
    {
        static::$classToFactory[$className] = $factoryClass;
        return $this;
    }

    /**
     * @param string $className
     * @param string $alias
     * @return DIContainer
     */
    public function registerAlias(string $className, string $alias): DIContainerInterface
    {
        static::$classToAlias[$alias] = $className;
        class_alias($className, $alias);
        return $this;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function has(string $className): bool
    {
        return !empty(static::$classToAlias[$className]) || !empty(static::$classToFactory[$className]);
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
