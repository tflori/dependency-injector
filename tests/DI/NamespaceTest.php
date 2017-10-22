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

        $result = DI::get('cache');

        self::assertRegExp('/^' . preg_quote(Factory\Cache::class) . '#\d+$/', $result);
    }

    /** @test */
    public function lastInFirstOut()
    {
        DI::registerNamespace(Factory::class);
        DI::registerNamespace(Factories::class);

        $result = DI::get('cache');

        self::assertRegExp('/^' . preg_quote(Factories\Cache::class) . '#\d+$/', $result);
    }

    /** @test */
    public function singletonFactoryByDefault()
    {
        DI::registerNamespace(Factory::class);
        $memcache = DI::get('cache');

        $result = DI::get('cache');

        self::assertSame($memcache, $result);
    }

    /** @test */
    public function nonSingletonFactory()
    {
        DI::registerNamespace(Factories::class);
        $memcache = DI::get('cache');

        $result = DI::get('cache');

        self::assertNotSame($memcache, $result);
    }
}
