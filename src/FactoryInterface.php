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
     * Share the created instance
     *
     * @param bool $share
     */
    public function share(bool $share = true);

    /**
     * Returns weather the instance should be shared or not
     *
     * @return bool
     */
    public function isShared(): bool;

    /**
     * Build the product of this factory.
     *
     * @return mixed
     */
    public function build();
}
