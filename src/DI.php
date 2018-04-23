<?php

namespace DependencyInjector;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class DI
 *
 * A static dependency injection container.
 *
 * Example usage:
 * DI::set('hello', function() { return 'hello'; });
 * DI::set('world', function() { return 'world'; });
 * echo DI:get('hello') . " " . DI:.get('world') . "!\n";
 */
class DI
{
    /** @var Container */
    protected static $container;

    /**
     * Alias for DI::get($name). Example:
     * DI::get('db') === DI::db()
     *
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        return self::getContainer()->get($name, ...$args);
    }

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
    public static function get(string $name, ...$args)
    {
        return self::getContainer()->get($name, ...$args);
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
    public static function has(string $name): bool
    {
        return self::getContainer()->has($name);
    }

    /**
     * Stores a dependency for $name using $getter.
     *
     * Returns the created factory or null when a value was given.
     *
     * $name can also be an array in the form `[$name => $getter]` or `[$name => [$getter, $share, $instance]]`.
     *
     * Possible values for $getter and the result:
     * | Description                         | Example                   | Result           |
     * |-------------------------------------|---------------------------|------------------|
     * | Callable or closure                 | ['Config', 'init']        | CallableFactory  |
     * | Class using singleton pattern       | MySingleton::class        | SingletonFactory |
     * | Instance of FactoryInterface        | new MyFactory($container) | MyFactory        |
     * | Class implementing FactoryInterface | MyFactory::class          | MyFactory        |
     * | Other classes                       | MyService::class          | ClassFactory     |
     * | non callable                        | new MyService             | null             |
     *
     * @deprecated This method is for backward compatibility only. Please use instance, share and add in the future.
     * @param string|array $name
     * @param null         $getter
     * @param bool         $share
     * @param bool         $instance
     * @return array|FactoryInterface|null
     */
    public static function set($name, $getter = null, bool $share = true, bool $instance = false)
    {
        if (is_array($name)) {
            $dependencies = $name;
            $factories = [];
            foreach ($dependencies as $name => $dependency) {
                $params = is_array($dependency) && !is_callable($dependency) ? $dependency : [$dependency];
                /** @noinspection PhpDeprecationInspection */
                // this is just how it was before and we are already in this deprecated method
                $factories[$name] = self::set($name, ...$params);
            }
            return $factories;
        }

        if ($instance ||
            !$getter instanceof FactoryInterface &&
            (!is_string($getter) || !class_exists($getter)) &&
            !is_callable($getter)
        ) {
            return self::getContainer()->instance($name, $getter);
        }

        if ($share) {
            return self::getContainer()->share($name, $getter);
        }

        return self::getContainer()->add($name, $getter);
    }

    /**
     * Stores a dependency for $name as instance.
     *
     * **Careful**: even when $instance is a callable it will not get executed. Instead you will
     * get the callable back.
     *
     * @param string $name
     * @param mixed  $instance
     */
    public static function instance(string $name, $instance)
    {
        self::getContainer()->instance($name, $instance);
    }

    /**
     * Adds a dependency for $name using $getter.
     *
     * When $getter results in a FactoryInterface instance share is called afterwards.
     *
     * @see self::set() for a description of $getter
     * @param string $name
     * @param mixed  $getter
     * @return FactoryInterface|null
     */
    public static function share(string $name, $getter): FactoryInterface
    {
        return self::getContainer()->share($name, $getter);
    }

    /**
     * Adds a dependency for $name using $getter.
     *
     * @param string $name
     * @param mixed  $getter
     * @return FactoryInterface|null
     */
    public static function add(string $name, $getter): FactoryInterface
    {
        return self::getContainer()->add($name, $getter);
    }

    /**
     * Creates an alias $name to the dependency / target $origin.
     *
     * @param string $origin
     * @param string $name
     */
    public static function alias(string $origin, string $name)
    {
        self::getContainer()->alias($origin, $name);
    }

    /**
     * Register namespace $namespace to search for factories.
     *
     * When a dependency is requested that is unknown the registered namespaces are searched for a class
     * with basename `ucfirst($name)` in LIFO order (last in - first out).
     *
     * @param string $namespace
     */
    public static function registerNamespace(string $namespace)
    {
        self::getContainer()->registerNamespace($namespace);
    }

    /**
     * Delete a dependency, instance and/or alias with $name.
     *
     * @param string $name
     */
    public static function delete(string $name)
    {
        self::getContainer()->delete($name);
    }

    /**
     * Reset the static DI by replacing the Container
     *
     * @return Container The created Container
     */
    public static function reset()
    {
        return self::$container = new Container();
    }

    /**
     * Overwrite the Container
     *
     * @param Container $container
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * Get the current Container
     *
     * @return Container
     */
    public static function getContainer(): Container
    {
        if (!isset(self::$container)) {
            self::$container = new Container();
        }

        return self::$container;
    }

    /**
     * @codeCoverageIgnore We never instantiate this class
     */
    private function __construct()
    {
    }
}
