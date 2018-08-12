<?php

namespace VS\DIContainer\Injector;

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
     * @param string|object $class
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
     * @param $function
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
            if ($reflectionParameter->isOptional()) {
                if (!empty($params[$i])) {
                    $returnParams[] = $params[$i];
                    $i++;
                }
                continue;
            }

            if ($reflectionParameter->getClass()) {
                $returnParams[] = static::injectClass($reflectionParameter->getClass()->getName());
            } elseif (!empty($params[$i])) {
                $returnParams[] = $params[$i];
                $i++;
            }
        }

        return $returnParams;
    }
}