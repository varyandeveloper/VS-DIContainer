<?php

namespace VS\DIContainer;

use VS\DIContainer\Injector\{
    Injector, InjectorException
};

const INJECTOR_INJECT_CLASS     = 1;
const INJECTOR_INJECT_METHOD    = 2;
const INJECTOR_INJECT_FUNCTION  = 3;

if (!function_exists('container')) {
    /**
     * @param string|null $className
     * @return object|DIContainer
     * @throws InjectorException
     */
    function container(string $className = null): object
    {
        static $container;

        if (!$container) {
            $container = new DIContainer;
        }

        return null !== $className ? $container->get($className) : $container;
    }
}

if (!function_exists('injector')) {
    /**
     * @param int|null $injectType
     * @param mixed ...$params
     * @return mixed
     * @throws InjectorException
     */
    function injector(int $injectType, ...$params)
    {
        if (!in_array($injectType, [INJECTOR_INJECT_CLASS, INJECTOR_INJECT_METHOD, INJECTOR_INJECT_FUNCTION])) {
            throw new InjectorException('Allowed injection of Class, Method or Function: use constants starting with INJECTOR_ prefix');
        }

        if ($injectType === INJECTOR_INJECT_METHOD) {
            return Injector::injectMethod(...$params);
        } elseif ($injectType === INJECTOR_INJECT_CLASS) {
            return Injector::injectClass(...$params);
        } else {
            return Injector::injectFunction(...$params);
        }
    }
}