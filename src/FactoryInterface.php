<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * FactoryInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * Build the product of this factory and return an instance.
     *
     * Sharing has to be handled here.
     *
     * @return mixed
     */
    public function getInstance();
}
