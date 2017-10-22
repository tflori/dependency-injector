<?php

namespace DependencyInjector\Test\example\Factories;

use DependencyInjector\Factory;

class Memcache extends Factory
{
    public static $singleton = false;

    public static function build()
    {
        return new \Memcache();
    }
}
