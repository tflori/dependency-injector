<?php

namespace DependencyInjector\Factory;

use DependencyInjector\FactoryInterface;
use Psr\Container\ContainerInterface;

class SingletonFactory implements FactoryInterface
{
    /** @var string */
    protected $class;

    public function __construct(ContainerInterface $container, string $class = null)
    {
        $this->class = $class;
    }

    /**
     * Build the product of this factory.
     *
     * @param array $args
     * @return mixed
     */
    public function getInstance(...$args)
    {
        return call_user_func_array([$this->class, 'getInstance'], $args);
    }
}
