<?php

namespace DependencyInjector;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id   Identifier of the entry to look for.
     * @param array  $args Any additional arguments for non shared getters
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id, ...$args)
    {
        // convert alias

        // return instance

        // build dependency

        // search for factory (WITHOUT REFLECTION!)

        return $id;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        // check for alias

        // check for instance

        // check for dependency

        // check for factory

        return false;
    }

    public function set(string $name, $getter, bool $shared = true, bool $instance = false)
    {
        // call instance if $instance

        // call share if $shared

        // call add otherwise
    }

    public function instance(string $name, $instance)
    {
        // delete existing alias

        // store instance
    }

    public function share(string $name, $getter)
    {
        // call add

        // set shared
    }

    public function add(string $name, $getter)
    {
        // delete existing alias

        // if is factory instance $getter store getter

        // if is class $getter create a reflection

            // if is factory class $getter store a new instance

            // elseif is singleton create a SingletonFactory

            // else create a ClassFactory

        // elseif is callable $getter create a CallableFactory

        // else call instance
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
     */
    public function alias(string $origin, string $name)
    {
        // check for $name in instances
        // check for $name as dependency
        // check for $name in factories

        // $this->has($origin) ?

        // store alias
    }

    /**
     * Delete dependency $name
     *
     * @param string $name
     */
    public function delete(string $name)
    {
        // remove existing instance

        // remove existing dependency

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
