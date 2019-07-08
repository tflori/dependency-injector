<?php

namespace DependencyInjector;

use DependencyInjector\Factory\CallableFactory;
use DependencyInjector\Factory\ClassFactory;
use DependencyInjector\Factory\SingletonFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /** @var string[] */
    protected $namespaces = [];

    /** @var FactoryInterface[] */
    protected $factories = [];

    /** @var PatternFactoryInterface[] */
    protected $patternFactories = [];

    public function __construct()
    {
        $this->instance('container', $this);
    }

    /**
     * Finds an entry of the container by $name and returns the instance.
     *
     * @param string $name Identifier of the entry to look for.
     * @param array  $args Any additional arguments for non shared getters
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($name, ...$args)
    {
        $factory = $this->resolve($name);
        try {
            if ($factory instanceof PatternFactoryInterface) {
                return $factory->getInstance($name);
            }
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            // a concrete factory could use this arguments
            return $factory->getInstance(...$args);
        } catch (\Throwable $t) {
            throw new Exception('Unexpected exception while resolving ' . $name, 0, $t);
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @codeCoverageIgnore Just an alias for get
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @codeCoverageIgnore Just an alias for get
     */
    public function __call($name, $args)
    {
        return $this->get($name, ...$args);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($name)` returning true does not mean that `get($name)` will not throw an exception.
     * It does however mean that `get($name)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $name Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($name)
    {
        try {
            $this->resolve($name);
            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * @param string $name
     * @return bool
     * @codeCoverageIgnore Just an alias for has
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Instantly create an object of $class
     *
     * @param string $class
     * @param mixed  ...$args
     * @return mixed
     */
    public function make(string $class, ...$args)
    {
        if ($this->has($class)) {
            return $this->get($class, ...$args);
        }

        $this->add($class, $class);
        return $this->get($class, ...$args);
    }

    /**
     * Returns the factory stored for this $name.
     *
     * This also searches for FactoryInterfaces in the registered namespaces.
     *
     * @param $name
     * @return FactoryInterface|null
     * @throws NotFoundException
     */
    protected function resolve($name): FactoryInterface
    {
        if (!isset($this->factories[$name])) {
            foreach ($this->namespaces as $namespace) {
                $class = sprintf($namespace, ucfirst($name));
                if (class_exists($class)) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    // it will not throw class does not exists - we checked that before
                    $reflection = new \ReflectionClass($class);
                    if ($reflection->implementsInterface(FactoryInterface::class)) {
                        $this->factories[$name] = new $class($this);
                        break;
                    }
                }
            }
        }

        if (!isset($this->factories[$name])) {
            foreach ($this->patternFactories as $factory) {
                if ($factory->matches($name)) {
                    return $factory;
                }
            }
        }

        if (!isset($this->factories[$name])) {
            throw new NotFoundException('Name ' . $name . ' could not be resolved');
        }

        return $this->factories[$name];
    }

    /**
     * Add a instance (or any other value) to the container.
     *
     * @param string $name
     * @param mixed  $instance
     * @return FactoryInterface
     */
    public function instance(string $name, $instance): FactoryInterface
    {
        return $this->factories[$name] = new Instance($this, $instance);
    }

    /**
     * Shortcut to `$c->add('service', $getter)->share()`
     *
     * @param string $name
     * @param mixed  $getter
     * @return FactoryInterface|SharableFactoryInterface
     * @throws ContainerExceptionInterface
     */
    public function share(string $name, $getter): FactoryInterface
    {
        $factory = $this->add($name, $getter);
        if ($factory instanceof SharableFactoryInterface) {
            $factory->share();
        }
        return $factory;
    }

    /**
     * Add a factory for $name
     *
     * Getter can be a instance or class name of a FactoryInterface, a callable or any other Class name.
     *
     * @param string $name
     * @param mixed  $getter
     * @return FactoryInterface|SharableFactoryInterface
     * @throws ContainerExceptionInterface
     */
    public function add(string $name, $getter): FactoryInterface
    {
        $factory = null;
        if ($getter instanceof FactoryInterface) {
            $factory = $getter;
        } elseif (is_string($getter) && class_exists($getter)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            // it will not throw class does not exists - we checked that before
            $reflection = new \ReflectionClass($getter);
            if ($reflection->implementsInterface(FactoryInterface::class)) {
                $factory = new $getter($this);
            } elseif ($reflection->getConstructor() &&
                      $reflection->getConstructor()->isPrivate() &&
                      is_callable([$getter, 'getInstance'])
            ) {
                $factory = new SingletonFactory($this, $getter);
            } else {
                $factory = new ClassFactory($this, $getter);
            }
        } elseif (is_callable($getter)) {
            $factory = new CallableFactory($this, $getter);
        }

        if (!$factory) {
            throw new Exception('$getter is invalid for dependency. Maybe you want to add an instance instead?');
        }

        if ($factory instanceof PatternFactoryInterface) {
            $this->patternFactories[] = $factory;
        } else {
            $this->factories[$name] = $factory;
        }

        return $factory;
    }

    public function addPatternFactory(PatternFactoryInterface $patternFactory)
    {
        $this->patternFactories[] = $patternFactory;
    }

    /**
     * Store an alias $name for $origin
     *
     * e. G. `$container->alias('DatabaseConnection', 'db')`
     *
     * The order is the same as symlink is using.
     *
     * @param string $origin
     * @param string $name
     * @throws ContainerExceptionInterface
     */
    public function alias(string $origin, string $name)
    {
        if ($this->has($name)) {
            $factory = $this->resolve($name);
            if (!$factory instanceof Alias) {
                /** @noinspection PhpUnhandledExceptionInspection */
                // it will not throw class does not exists - we already have an object
                $reflection = new \ReflectionClass($factory);
                throw new Exception(sprintf('%s for %s already exists', ($reflection)->getShortName(), $name));
            }
        }

        if (!$this->has($origin)) {
            throw new Exception('Origin ' . $origin . ' could not be resolved');
        }

        $this->factories[$name] = new Alias($this, $origin);
    }

    /**
     * Delete dependency $name
     *
     * @param string $name
     */
    public function delete(string $name)
    {
        if (array_key_exists($name, $this->factories)) {
            unset($this->factories[$name]);
        }
    }

    /**
     * Register $namespace for FactoryInterfaces
     *
     * @param string $namespace
     * @param string $suffix
     */
    public function registerNamespace(string $namespace, string $suffix = '')
    {
        $classTemplate = rtrim($namespace, '\\') . '\\%s' . $suffix;

        $p = array_search($classTemplate, $this->namespaces);
        if ($p === 0) {
            return; // this does not have an explicit check
        } elseif ($p !== false) {
            array_splice($this->namespaces, $p, 1);
        }

        array_unshift($this->namespaces, $classTemplate);
    }
}
