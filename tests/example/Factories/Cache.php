<?php

namespace DependencyInjector\Test\example\Factories;

use DependencyInjector\Factory;

class Cache extends Factory
{
    private static $i = 1;

    public static $singleton = false;

    public static function build()
    {
        return __CLASS__ . '#' . self::$i++;
    }
}
