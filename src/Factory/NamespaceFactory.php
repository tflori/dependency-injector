<?php

namespace DependencyInjector\Factory;

use DependencyInjector\Factory\Concern\CreatesClassInstances;
use DependencyInjector\PatternFactoryInterface;
use Psr\Container\ContainerInterface;

class NamespaceFactory implements PatternFactoryInterface
{
    use CreatesClassInstances;

    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $namespace;

    public function __construct(ContainerInterface $container, string $namespace = null)
    {
        $this->namespace = $namespace;
        $this->container = $container;
    }


    public function matches(string $name): bool
    {
        return strncmp($name, $this->namespace, strlen($this->namespace)) === 0;
    }

    public function getInstance(string $name = null)
    {
        $this->class = $name;
        return $this->build();
    }
}
