<?php

namespace VS\DIContainer;

/**
 * Interface InvokableFactoryInterface
 * @package VS\DIContainer
 */
interface InvokableFactoryInterface
{
    /**
     * @param DIContainerInterface $factory
     * @param string $className
     * @param array $params
     * @return mixed
     */
    public function __invoke(DIContainerInterface $factory, string $className, array $params = []);
}