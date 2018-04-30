<?php

namespace DependencyInjector\Factory;

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
     * @param array $args
     * @return mixed
     */
    protected function build(...$args)
    {
        return call_user_func_array($this->callable, $args);
    }
}
