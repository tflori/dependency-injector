<?php

namespace DependencyInjector\Test\example\Singleton;

/**
 * Class MySingleton
 *
 * Example of a singleton pattern.
 */
class MySingleton
{
    /** @var MySingleton */
    private static $instance;

    /**
     * @return MySingleton
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Example function of the singleton. It Could also be any other result (for example a Database result).
     *
     * @return string
     */
    public function getResult()
    {
        return 'defaultResult';
    }

    private function __construct()
    {
    }
    private function __clone()
    {
    }
}
