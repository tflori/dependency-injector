<?php

namespace DependencyInjector\Test\example\Factory;

use DependencyInjector\Factory;

class Memcache extends Factory
{
    public static function build()
    {
        return new \Memcached();
    }
}
