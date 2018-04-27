<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class Alias implements FactoryInterface
{
    /** @var string */
    protected $origin;

    /** @var ContainerInterface */
    protected $container;

    /**
     * FactoryInterface constructor.
     *
     * @param ContainerInterface $container
     * @param string             $origin
     */
    public function __construct(ContainerInterface $container, string $origin = null)
    {
        $this->container = $container;
        $this->origin = $origin;
    }

    /**
     * Build the product of this factory and return an instance.
     *
     * Sharing has to be handled here.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->container->get($this->origin);
    }
}
