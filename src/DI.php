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

    /**
     * Get a previously defined dependency identified by $name.
     *
     * @param string $name
     * @throws Exception
     */
    public static function get($name) {
        throw new Exception("Unknown dependency '" . $name . "'");
    }
}
