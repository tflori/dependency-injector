<?php

namespace DependencyInjector\Test\Examples;

class AdvancedSingleton
{
    /** @var AdvancedSingleton[] */
    protected static $instances = [];

    public $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    private function __clone()
    {
    }

    public static function getInstance(string $name)
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }

        return self::$instances[$name];
    }
}
