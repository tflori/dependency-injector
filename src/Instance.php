<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class Instance implements FactoryInterface
{
    /** @var mixed */
    protected $instance;

    public function __construct(ContainerInterface $container, $instance = null)
    {
        $this->instance = $instance;
    }

    /**
     * Build the product of this factory.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
