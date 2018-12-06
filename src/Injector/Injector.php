<?php

namespace VS\DIContainer\Injector;

use VS\DIContainer\DIContainer;

/**
 * Class Injector
 * @package VS\DIContainer\Injector
 */
class Injector implements InjectorInterface
{
    /**
     * @param string $className
     * @param mixed ...$params
     * @return object
     * @throws InjectorException
     */
    public static function injectClass(string $className, ...$params): object
    {
        try {
            $reflectionClass = new \ReflectionClass($className);
            $constructor = $reflectionClass->getConstructor();

            // If constructor exists then inject constructor params
            if ($constructor) {
                $params = static::getMergedPassedParamsAndInjectableParams($constructor, $params);
            } else {
                return $reflectionClass->newInstanceWithoutConstructor();
            }

            return $reflectionClass->newInstance(...$params);

        } catch (\ReflectionException $exception) {
            throw new InjectorException('Something went wrong can\'t inject class ' . $className);
        }
    }

    /**
     * @param object|string $class
     * @param string $methodName
     * @param mixed ...$params
     * @return mixed
     * @throws InjectorException
     */
    public static function injectMethod($class, string $methodName, ...$params)
    {
        if (!is_string($class) && !is_object($class)) {
            throw new InjectorException('First parameter should be either a string or an object.');
        }

        if (is_string($class)) {
            $class = static::injectClass($class);
        }

        try {
            $reflectionMethod = new \ReflectionMethod($class, $methodName);
            $params = static::getMergedPassedParamsAndInjectableParams($reflectionMethod, $params);
        } catch (\ReflectionException $exception) {
            throw new InjectorException('Something went wrong can\'t inject method ' . $methodName . ' of class ' . get_class($class));
        }

        return $reflectionMethod->invoke($class, ...$params);
    }

    /**
     * @param callable|\Closure|string $function
     * @param mixed ...$params
     * @return mixed
     * @throws InjectorException
     */
    public static function injectFunction($function, ...$params)
    {
        try {
            $reflectionFunction = new \ReflectionFunction($function);
            $params = static::getMergedPassedParamsAndInjectableParams($reflectionFunction, $params);
        } catch (\ReflectionException $exception) {
            throw new InjectorException('Something went wrong can\'t inject function ');
        }

        return $reflectionFunction->invoke(...$params);
    }

    /**
     * @param \ReflectionFunctionAbstract $functionAbstract
     * @param array $params
     * @return array
     * @throws InjectorException
     * @throws \ReflectionException
     */
    public static function getMergedPassedParamsAndInjectableParams(\ReflectionFunctionAbstract $functionAbstract, array $params): array
    {
        $paramsToInject = $functionAbstract->getParameters();
        $returnParams = [];
        $i = 0;

        if (count($params) === count($paramsToInject)) {
            return $params;
        }

        foreach ($paramsToInject as $reflectionParameter) {
            if ($reflectionParameter->getClass()) {
                $className = $reflectionParameter->getClass()->getName();
                if (isset($params[$i]) && $params[$i] instanceof $className) {
                    $returnParams[] = $params[$i];
                    $i++;
                } else {
                    $returnParams[] = self::getContainer()->has($className)
                        ? self::getContainer()->get($className)
                        : static::injectClass($className);
                }
            } elseif (array_key_exists($i, $params)) {
                $returnParams[] = $params[$i];
                $i++;
            }
        }

        return $returnParams;
    }

    /**
     * @return DIContainer
     */
    protected static function getContainer(): DIContainer
    {
        static $container;
        if (!$container) {
            $container = new DIContainer;
        }

        return $container;
    }
}