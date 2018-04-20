<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class SingletonFactory extends AbstractFactory
{
    /** @var string */
    protected $class;

    public function __construct(ContainerInterface $container, string $class = null)
    {
        parent::__construct($container);
        $this->class = $class;
    }

    /**
     * Build the product of this factory.
     *
     * @param array $args
     * @return mixed
     */
    public function build(...$args)
    {
        return call_user_func([$this->class, 'getInstance']);
    }

    public function share(bool $share = true)
    {
        return; // the singleton factory cannot be shared
    }
}
