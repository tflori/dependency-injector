<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class SingletonFactory extends AbstractFactory
{
//    /** @var string */
//    protected $class;
//
//    public function __construct(ContainerInterface $container, string $class = null)
//    {
//        $this->class = $class;
//        parent::__construct($container);
//    }

    /**
     * Build the product of this factory.
     *
     * @param null $name
     * @return mixed
     */
    public function build($name = null)
    {
//        if ($name && !$this->isShared()) {
//            return call_user_func([$this->class, 'getInstance'], $name);
//        }
//
//        return call_user_func([$this->class, 'getInstance']);
    }
}
