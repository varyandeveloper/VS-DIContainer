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
    protected const MAX_PARENTS_FOR_ABSTRACT_FACTORY_CHILDREN = 5;

    protected static $abstractFactories = [];

    protected static $classToFactory = [];

    protected static $classToAlias = [];

    protected static $singletonState = [];

    protected static $factoryToConfig = [];

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
     * @param string|null $factoryClass
     * @param string|null $alias
     * @return DIContainerInterface
     * @throws \ReflectionException
     */
    public function register(string $className, ?string $factoryClass = null, ?string $alias = null): DIContainerInterface
    {
        if (!$factoryClass && !$alias) {
            trigger_error(
                'Using register method without factory and alias dose not have an effect',
                E_USER_WARNING
            );
            return $this;
        }

        if ($factoryClass) {
            $this->registerFactory($className, $factoryClass);
        }

        if ($alias) {
            $this->registerAlias($className, $alias);
        }

        return $this;
    }

    /**
     * @param string $className
     * @param string $factoryClass
     * @return DIContainerInterface
     * @throws \ReflectionException
     */
    public function registerFactory(string $className, string $factoryClass): DIContainerInterface
    {
        $reflection = new \ReflectionClass($className);
        if ($reflection->isAbstract()) {
            self::$abstractFactories[$className] = $factoryClass;
        } else {
            static::$classToFactory[$className] = $factoryClass;
        }
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
     * @throws \ReflectionException
     */
    public function has(string $className): bool
    {
        return
            !empty(static::$classToAlias[$className]) ||
            !empty(static::$classToFactory[$className]) ||
            $this->findTopParentByHierarchy($className);
    }

    /**
     * @param string $className
     * @param mixed ...$params
     * @return object
     * @throws InjectorException
     * @throws \ReflectionException
     */
    public function get(string $className, ...$params): object
    {
        if (!empty(static::$classToAlias[$className])) {
            $className = static::$classToAlias[$className];
        }

        if (!empty(static::$classToFactory[$className])) {
            $factory = static::$classToFactory[$className];
            $this->validateClass($factory);
            $object = new $factory;
            return $object($this, $className, $params);
        } else {

            $factory = $this->findTopParentByHierarchy($className);

            if ($factory) {
                $this->registerFactory($className, $factory);
                return $this->get($className, ...$params);
            }

            $this->validateClass($className);
            return Injector::injectClass($className, ...$params);
        }
    }

    /**
     * @param string $className
     * @return object
     * @throws InjectorException
     * @throws \ReflectionException
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
     * @return string|null
     * @throws \ReflectionException
     */
    protected function findTopParentByHierarchy(string $className): ?string
    {
        $reflection = new \ReflectionClass($className);
        $i = static::MAX_PARENTS_FOR_ABSTRACT_FACTORY_CHILDREN;

        while ($reflection->getParentClass() && $i-- && !$reflection->isAbstract()) {
            $reflection = $reflection->getParentClass();
        }

        return self::$abstractFactories[$reflection->getName()] ?? null;
    }

    protected function validateClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new DIContainerException('Class factory ' . $className . ' dose not exists');
        }
    }
}
