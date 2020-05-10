<?php

namespace DependencyInjector\Factory;

use DependencyInjector\Factory\Concern\CreatesClassInstances;
use DependencyInjector\PatternFactoryInterface;
use Psr\Container\ContainerInterface;

class NamespaceFactory extends AbstractFactory implements PatternFactoryInterface
{
    use CreatesClassInstances;

    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $namespace;

    /** @var array */
    protected $instances = [];

    public function __construct(ContainerInterface $container, string $namespace = null)
    {
        $this->namespace = $namespace;
        $this->container = $container;
    }


    public function matches(string $name): bool
    {
        return strncmp($name, $this->namespace, strlen($this->namespace)) === 0;
    }

    public function getInstance($name = null, ...$args)
    {
        if ($this->isShared()) {
            if (!isset($this->instances[$name])) {
                $this->instances[$name] = $this->build();
            }

            return $this->instances[$name];
        }

        $this->class = $name;
        return $this->build(...$args);
    }
}
