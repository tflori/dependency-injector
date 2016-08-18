<?php

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
class DependencyInjector {

    /**
     * Get a previously defined dependency identified by $name.
     *
     * @param string $name
     * @throws DependencyInjectorException
     */
    public static function get($name) {
        throw new DependencyInjectorException("Unknown dependency '" . $name . "'");
    }
}
