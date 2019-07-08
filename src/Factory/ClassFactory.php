<?php

namespace DependencyInjector\Factory;

use DependencyInjector\Factory\Concern\CreatesClassInstances;
use Psr\Container\ContainerInterface;

class ClassFactory extends AbstractFactory
{
    use CreatesClassInstances;

    public function __construct(ContainerInterface $container, string $class = null)
    {
        parent::__construct($container);
        $this->class = $class;
    }
}
