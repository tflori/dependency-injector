<?php

namespace DependencyInjector\Factory\Concern;

use DependencyInjector\Factory\StringArgument;
use Psr\Container\ContainerInterface;

trait CreatesClassInstances
{
    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $class;

    /** @var array */
    protected $arguments = [];

    /** @var array */
    protected $methodCalls = [];

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
     * Add call to $method with $args that get executed after initialization
     *
     * @param string $method
     * @param mixed ...$args
     * @return $this
     */
    public function addMethodCall(string $method, ...$args)
    {
        array_push($this->methodCalls, [$method, $args]);
        return $this;
    }

    /**
     * Build the product of this factory.
     *
     * @noinspection PhpDocSignatureInspection
     * @param mixed $additionalArgs...
     * @return mixed
     */
    protected function build()
    {
        $args = array_map([$this, 'resolveArg'], $this->arguments);

        $instance = new $this->class(...$args, ...func_get_args());

        foreach ($this->methodCalls as $methodCall) {
            call_user_func_array([$instance, $methodCall[0]], array_map([$this, 'resolveArg'], $methodCall[1]));
        }

        return $instance;
    }

    /**
     * Resolve $arg through container
     *
     * @param $arg
     * @return mixed|string
     */
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
