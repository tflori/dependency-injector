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
    protected static $instances    = [];
    protected static $dependencies = [];
    protected static $aliases      = [];
    protected static $namespaces   = [];

    /**
     * Get a previously defined dependency identified by $name.
     *
     * @param string $name
     * @throws Exception
     * @return mixed
     */
    public static function get($name)
    {
        if (isset(self::$aliases[$name])) {
            $name = self::$aliases[$name];
        }

        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        if (isset(self::$dependencies[$name])) {
            if (self::$dependencies[$name]['singleton']) {
                self::$instances[$name] = call_user_func(self::$dependencies[$name]['getter']);
                return self::$instances[$name];
            }

            return call_user_func(self::$dependencies[$name]['getter']);
        }

        foreach (self::$namespaces as $namespace) {
            $factory = rtrim($namespace, '\\') . '\\' . ucfirst($name);
            if (class_exists($factory) && is_callable([$factory, 'build'])) {
                if (isset($factory::$singleton) && $factory::$singleton) {
                    self::$instances[$name] = $factory::build();
                    return self::$instances[$name];
                }

                return $factory::build();
            }
        }

        if (class_exists($name)) {
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
     * @param string|array $name
     * @param mixed        $getter    The callable getter for this dependency or the value
     * @param bool         $singleton Save result from $getter for later request
     * @param bool         $isValue   Store $getter as value
     * @return void
     */
    public static function set($name, $getter = null, $singleton = true, $isValue = false)
    {
        if (is_array($name)) {
            $dependencies = $name;
            foreach ($dependencies as $name => $dependency) {
                $params = is_array($dependency) && !is_callable($dependency) ? $dependency : [$dependency];
                array_unshift($params, $name);
                self::set(...$params);
            }
            return;
        }

        if ($isValue) {
            self::$instances[$name] = $getter;
            return;
        }

        if (is_string($getter) && class_exists($getter)) {
            $getter = function () use ($getter) {
                $reflection = new \ReflectionClass($getter);

                if ($reflection->implementsInterface(FactoryInterface::class)) {
                    return $getter::build();
                }

                if ($reflection->hasMethod('__construct') && $reflection->getMethod('__construct')->isPrivate() &&
                    $reflection->hasMethod('getInstance')) {
                    return $getter::getInstance();
                }

                return new $getter;
            };
        }

        if (!is_callable($getter)) {
            self::$instances[$name] = $getter;
            return;
        }

        if (isset(self::$instances[$name])) {
            unset(self::$instances[$name]);
        }

        self::$dependencies[$name] = [
            'singleton' => $singleton,
            'getter'    => $getter
        ];
    }

    /**
     * Store an alias $name for $origin
     *
     * @param string $origin
     * @param string $name
     */
    public static function alias($origin, $name)
    {
        self::$aliases[$name] = $origin;
    }

    public static function registerNamespace($namespace)
    {
        array_unshift(self::$namespaces, $namespace);
    }

    /**
     * Resets the DependencyInjector
     *
     * @return void
     */
    public static function reset()
    {

        self::$instances    = [];
        self::$dependencies = [];
        self::$namespaces   = [];
    }

    /**
     * Removes dependency $name
     *
     * @param string $name
     */
    public static function delete($name)
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

    /**
     * @codeCoverageIgnore We never instantiate this class
     */
    private function __construct()
    {
    }
}
