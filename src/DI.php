<?php

namespace DependencyInjector;

/**
 * Class DependencyInjector
 *
 * This is a Dependency Injector. It gives the opportunity to define how to get a dependency from outside of the code
 * that needs the dependency. This is especially helpful for testing proposes when you need to mock a dependency.
 *
 * You can never get an instance of this class. For usage you have only two static methods.
 *
 * Example usage:
 * DependencyInjector::set('hello', function() { return 'hello'; });
 * DependencyInjector::set('world', function() { return 'world'; });
 * echo DependencyInjector:get('hello') . " " . DependencyInjector:.get('world') . "!\n";
 */
class DI
{
    protected static $instances     = [];
    protected static $dependencies = [];

    /**
     * Get a previously defined dependency identified by $name.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public static function get($name)
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        } elseif (isset(self::$dependencies[$name])) {
            if (self::$dependencies[$name]['singleton']) {
                self::$instances[$name] = call_user_func(self::$dependencies[$name]['getter']);
                return self::$instances[$name];
            }

            return call_user_func(self::$dependencies[$name]['getter']);

        } elseif (class_exists($name)) {
            $reflection = new \ReflectionClass($name);

            if ($reflection->getName() === $name) {
                self::$instances[$name] = $name;
                return self::$instances[$name];
            }
        }

        throw new Exception("Unknown dependency '" . $name . "'");
    }

    /**
     * Alias for DI::get($name). Example:
     * DI::get('same') === DI::same()
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {

        return self::get($name);
    }

    /**
     * Define a dependency.
     *
     * @param string $name
     * @param mixed  $getter    The callable getter for this dependency or the value
     * @param bool   $singleton Save result from $getter for later request
     * @param bool   $isValue   Store $getter as value
     * @return void
     */
    public static function set($name, $getter, $singleton = true, $isValue = false)
    {

        if (!$isValue && is_callable($getter)) {
            if (isset(self::$instances[$name])) {
                unset(self::$instances[$name]);
            }

            self::$dependencies[$name] = [
                'singleton' => $singleton,
                'getter'    => $getter
            ];
        } else {
            self::$instances[$name] = $getter;
        }
    }

    /**
     * Resets the DependencyInjector
     *
     * @return void
     */
    public static function reset()
    {

        self::$instances     = [];
        self::$dependencies = [];
    }

    /**
     * Removes dependency $name
     *
     * @param string $name
     */
    public static function unset($name)
    {

        if (isset(self::$instances[$name])) {
            unset(self::$instances[$name]);
        }

        if (isset(self::$dependencies[$name])) {
            unset(self::$dependencies[$name]);
        }
    }

    /**
     * Checks if dependency $name is defined
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {

        return isset(self::$instances[$name]) || isset(self::$dependencies[$name]);
    }

    private function __construct()
    {
    }
}
