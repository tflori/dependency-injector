<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class CallableFactory extends AbstractFactory
{
    protected $callable;

    public function __construct(ContainerInterface $container, callable $callable = null)
    {
        parent::__construct($container);
        $this->callable = $callable;
    }

    /**
     * Build the product of this factory.
     *
     * @return mixed
     */
    public function build(...$args)
    {
        return call_user_func_array($this->callable, $args);
    }
}
