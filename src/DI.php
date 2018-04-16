<?php

namespace DependencyInjector;

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

    public static function get(string $name, ...$args)
    {
        return self::getContainer()->get($name, ...$args);
    }

    public static function set($name, $getter = null, bool $singleton = true, bool $isValue = false)
    {
        if (is_array($name)) {
            $dependencies = $name;
            $factories = [];
            foreach ($dependencies as $name => $dependency) {
                $params = is_array($dependency) && !is_callable($dependency) ? $dependency : [$dependency];
                array_unshift($params, $name);
                $factories[$name] = self::getContainer()->set(...$params);
            }
            return $factories;
        }

        return self::getContainer()->set($name, $getter, $singleton, $isValue);
    }

    public static function instance(string $name, $instance)
    {
        self::getContainer()->instance($name, $instance);
    }

    public static function share(string $name, $getter)
    {
        self::getContainer()->share($name, $getter);
    }

    public static function add(string $name, $getter)
    {
        self::getContainer()->add($name, $getter);
    }

    public static function alias(string $origin, string $name)
    {
        self::getContainer()->alias($origin, $name);
    }

    public static function registerNamespace(string $namespace)
    {
        self::getContainer()->registerNamespace($namespace);
    }

    public static function delete(string $name)
    {
        self::getContainer()->delete($name);
    }

    public static function has(string $name)
    {
        self::getContainer()->has($name);
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
    protected static function getContainer()
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
