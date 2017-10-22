<?php

namespace DependencyInjector\Test\DI;

use DependencyInjector\DI;
use DependencyInjector\Test\example\Factory;
use DependencyInjector\Test\example\Factories;
use PHPUnit\Framework\TestCase;

class NamespaceTest extends TestCase
{
    protected function setUp()
    {
        DI::reset();
    }

    /** @test */
    public function usesTheFactoryFromRegisteredNamespace()
    {
        DI::registerNamespace(Factory::class);

        $result = DI::get('memcache');

        self::assertEquals(new \Memcached(), $result);
    }

    /** @test */
    public function lastInFirstOut()
    {
        DI::registerNamespace(Factories::class);
        DI::registerNamespace(Factory::class);

        $result = DI::get('memcache');

        self::assertEquals(new \Memcached(), $result);
    }

    /** @test */
    public function singletonFactoryByDefault()
    {
        DI::registerNamespace(Factory::class);
        $memcache = DI::get('memcache');

        $result = DI::get('memcache');

        self::assertSame($memcache, $result);
    }

    /** @test */
    public function nonSingletonFactory()
    {
        DI::registerNamespace(Factories::class);
        $memcache = DI::get('memcache');

        $result = DI::get('memcache');

        self::assertNotSame($memcache, $result);
    }
}
