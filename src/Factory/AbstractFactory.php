<?php

namespace DependencyInjector\Factory;

use DependencyInjector\Factory\Concern\IsSharable;
use DependencyInjector\SharableFactoryInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractFactory implements SharableFactoryInterface
{
    use IsSharable;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
