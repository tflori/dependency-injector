<?php

namespace DependencyInjector\Factory;

use DependencyInjector\FactoryInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractFactory implements FactoryInterface
{
    /** @var ContainerInterface */
    protected $container;

    /** @var bool */
    protected $shared = false;

    /** @var mixed */
    protected $instance;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function share(bool $share = true)
    {
        $this->shared = $share;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function getInstance(...$args)
    {
        if ($this->isShared()) {
            if (!$this->instance) {
                $this->instance = $this->build();
            }

            return $this->instance;
        }

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        // a concrete factory could use this arguments
        return $this->build(...$args);
    }

    /**
     * This method builds the instance.
     *
     * @return mixed
     */
    abstract protected function build();
}
