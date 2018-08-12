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
     * @param array $params
     * @return mixed
     */
    public function __invoke(DIContainerInterface $factory, array $params = []);
}