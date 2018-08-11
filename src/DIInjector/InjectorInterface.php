<?php

namespace VS\DIContainer\Injector;

/**
 * Interface InjectorInterface
 * @package VS\DIContainer\Injector
 */
interface InjectorInterface
{
    /**
     * @param string $className
     * @param mixed ...$params
     * @return object
     */
    public static function injectClass(string $className, ...$params): object;

    /**
     * @param string|object $class
     * @param string $methodName
     * @param mixed ...$params
     * @return mixed
     */
    public static function injectMethod($class, string $methodName, ...$params);

    /**
     * @param string|callable|\Closure $function
     * @param mixed ...$params
     * @return mixed
     */
    public static function injectFunction($function, ...$params);

    /**
     * @param \ReflectionFunctionAbstract $functionAbstract
     * @param array $params
     * @return array
     */
    public static function getMergedPassedParamsAndInjectableParams(\ReflectionFunctionAbstract $functionAbstract, array $params): array;
}