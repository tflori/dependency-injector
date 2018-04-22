<?php

namespace DependencyInjector\Factory;

use Psr\Container\ContainerInterface;

class ClassFactory extends AbstractFactory
{
    /** @var string */
    protected $class;

    /** @var array */
    protected $arguments = [];

    /** @var array */
    protected $methodCalls = [];

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

    public function addMethodCall(string $method, ...$args)
    {
        array_push($this->methodCalls, [$method, $args]);
        return $this;
    }

    /**
     * Build the product of this factory.
     *
     * @param array $additionalArgs
     * @return mixed
     */
    protected function build(...$additionalArgs)
    {
        $args = array_map([$this, 'resolveArg'], $this->arguments);

        $instance = new $this->class(...$args, ...$additionalArgs);

        foreach ($this->methodCalls as $methodCall) {
            call_user_func_array([$instance, $methodCall[0]], array_map([$this, 'resolveArg'], $methodCall[1]));
        }

        return $instance;
    }

    protected function resolveArg($arg)
    {
        if (is_string($arg) && $this->container->has($arg)) {
            return $this->container->get($arg);
        } elseif ($arg instanceof StringArgument) {
            return $arg->getString();
        } else {
            return $arg;
        }
    }
}
