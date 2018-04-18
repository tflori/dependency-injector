<?php

namespace DependencyInjector;

use DependencyInjector\Exception\NotFound;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /** @var array */
    protected $instances = [];

    /** @var string[] */
    protected $aliases = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
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
        if (array_key_exists($name, $this->aliases)) {
            $name = $this->aliases[$name];
        }

        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }

        // build dependency

        // search for factory (WITHOUT REFLECTION!)

        throw new NotFound(sprintf('Name %s could not be resolved', $name));
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
        if (array_key_exists($name, $this->aliases)) {
            $name = $this->aliases[$name];
        }

        if (array_key_exists($name, $this->instances)) {
            return true;
        }

        // check for dependency

        // check for factory

        return false;
    }

    public function set(string $name, $getter, bool $shared = true, bool $instance = false): ?FactoryInterface
    {
        if ($instance) {
            $this->instance($name, $getter);
            return null;
        }

        // call share if $shared

        // call add otherwise
    }

    public function instance(string $name, $instance)
    {
        if (array_key_exists($name, $this->aliases)) {
            unset($this->aliases[$name]);
        }

        $this->instances[$name] = $instance;
    }

    public function share(string $name, $getter): FactoryInterface
    {
        // call add

        // set shared
    }

    public function add(string $name, $getter): FactoryInterface
    {
        // delete existing alias

        // if is factory instance $getter store getter

        // if is class $getter create a reflection

            // if is factory class $getter store a new instance

            // elseif is singleton create a SingletonFactory

            // else create a ClassFactory

        // elseif is callable $getter create a CallableFactory

        // else throw
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
        if (array_key_exists($name, $this->instances)) {
            throw new Exception(sprintf('Instance for %s already exists', $name));
        }
        // check for $name as dependency
        // check for $name in factories

        if (!$this->has($origin)) {
            throw new Exception(sprintf('Origin %s could not be resolved', $origin));
        }

        $this->aliases[$name] = $origin;
    }

    /**
     * Delete dependency $name
     *
     * @param string $name
     */
    public function delete(string $name)
    {
        if (array_key_exists($name, $this->instances)) {
            unset($this->instances[$name]);
        }

        if (array_key_exists($name, $this->aliases)) {
            unset($this->aliases[$name]);
        }

        // remove existing alias -> MUST NOT remove the linked dependency
    }

    /**
     * Register $namespace for FactoryInterfaces
     *
     * @param $namespace
     */
    public function registerNamespace(string $namespace)
    {
        // prepend the namespace

        // delete $namespace in registered namespaces
    }
}
