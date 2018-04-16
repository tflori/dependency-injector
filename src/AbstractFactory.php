<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

abstract class AbstractFactory implements FactoryInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var bool */
    protected $shared = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function share(bool $share = true)
    {
        $this->shared = $share;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }
}
