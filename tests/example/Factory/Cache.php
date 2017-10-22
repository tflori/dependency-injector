<?php

namespace DependencyInjector\Test\example\Factory;

use DependencyInjector\Factory;

class Cache extends Factory
{
    private static $i = 1;

    public static function build()
    {
        return __CLASS__ . '#' . self::$i++;
    }
}
