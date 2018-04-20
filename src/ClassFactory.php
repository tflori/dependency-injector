<?php

namespace DependencyInjector;

use Psr\Container\ContainerInterface;

class ClassFactory extends AbstractFactory
{
    /** @var string */
    protected $class;

    /** @var array */
    protected $arguments = [];

    public function __construct(ContainerInterface $container, string $class = null)
    {
        parent::__construct($container);
        $this->class = $class;
    }

    /**
     * Add constructor arguments
     *
     * Strings will be resolved from container.
     *
     * @param mixed ...$args
     * @return $this
     */
    public function addArguments(...$args)
    {
        array_push($this->arguments, ...$args);
        return $this;
    }

    /**
     * Build the product of this factory.
     *
     * @param array $additionalArgs
     * @return mixed
     */
    public function build(...$additionalArgs)
    {
        $args = [];

        foreach ($this->arguments as $arg) {
            if (is_string($arg) && $this->container->has($arg)) {
                $args[] = $this->container->get($arg);
            } elseif ($arg instanceof StringArgument) {
                $args[] = $arg->getString();
            } else {
                $args[] = $arg;
            }
        }

        return new $this->class(...$args, ...$additionalArgs);
    }
}
