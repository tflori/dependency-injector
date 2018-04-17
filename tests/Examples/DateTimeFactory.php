<?php

namespace DependencyInjector\Test\Examples;

use DependencyInjector\FactoryInterface;
use Psr\Container\ContainerInterface;

class DateTimeFactory implements FactoryInterface
{
    /**
     * FactoryInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
    }

    /**
     * Share the created instance
     *
     * @param bool $share
     */
    public function share(bool $share = true)
    {
        // cannot be shared
    }

    /**
     * Returns weather the instance should be shared or not
     *
     * @return bool
     */
    public function isShared(): bool
    {
        return false;
    }

    /**
     * Build the product of this factory.
     *
     * @param array $args
     * @return mixed
     */
    public function build(...$args)
    {
        return new \DateTime(...$args);
    }
}
