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
class DI {
    protected static $_instances = [];
    protected static $_dependencies = [];

    /**
     * Get a previously defined dependency identified by $name.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public static function get($name) {

        if (isset(self::$_instances[$name])) {

            return self::$_instances[$name];

        } elseif (isset(self::$_dependencies[$name])) {

            if (self::$_dependencies[$name]['singleton']) {

                self::$_instances[$name] = call_user_func(self::$_dependencies[$name]['getter']);
                return self::$_instances[$name];

            }

            return call_user_func(self::$_dependencies[$name]['getter']);

        } elseif (class_exists($name)) {

            $reflection = new \ReflectionClass($name);

            if ($reflection->getName() === $name) {

                self::$_instances[$name] = $name;
                return self::$_instances[$name];

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
    public static function __callStatic($name, $arguments) {

        return self::get($name);
    }

    /**
     * Define a dependency.
     *
     * @param string $name
     * @param mixed  $getter
     * @param bool   $singleton
     * @return void
     */
    public static function set($name, $getter, $singleton = true) {

        if (is_object($getter) && $getter instanceof \Closure && is_callable($getter)) {

            if (isset(self::$_instances[$name])) {
                unset(self::$_instances[$name]);
            }

            self::$_dependencies[$name] = [
                'singleton' => $singleton,
                'getter' => $getter
            ];

        } else {

            self::$_instances[$name] = $getter;

        }
    }

    /**
     * Resets the DependencyInjector
     *
     * @return void
     */
    public static function reset() {

        self::$_instances = [];
        self::$_dependencies = [];
    }

    /**
     * Removes dependency $name
     *
     * @param string $name
     */
    public static function unset($name) {

        if (isset(self::$_instances[$name])) {
            unset(self::$_instances[$name]);
        }

        if (isset(self::$_dependencies[$name])) {
            unset(self::$_dependencies[$name]);
        }
    }

    /**
     * Checks if dependency $name is defined
     *
     * @param string $name
     * @return bool
     */
    public static function has($name) {

        return isset(self::$_instances[$name]) || isset(self::$_dependencies[$name]);
    }

    private function __construct() {}
}
