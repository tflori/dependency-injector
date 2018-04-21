<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use DependencyInjector\Test\Examples\SomeService;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class DeleteTest extends MockeryTestCase
{
    /** @test */
    public function removesAnInstance()
    {
        $container = new Container();
        $container->instance('foo', 42);

        $container->delete('foo');

        self::assertFalse($container->has('foo'));
    }

    /** @test */
    public function removesAFactory()
    {
        $container = new Container();
        $container->add('service', SomeService::class);

        $container->delete('service');

        self::assertFalse($container->has('service'));
    }

    /** @test */
    public function removesAnAlias()
    {
        $container = new Container();
        $container->instance('foo', 42);
        $container->alias('foo', 'bar');

        $container->delete('bar');

        self::assertFalse($container->has('bar'));
    }

    /** @test */
    public function keepsTheOriginOfAnAlias()
    {
        $container = new Container();
        $container->instance('foo', 42);
        $container->alias('foo', 'bar');

        $container->delete('bar');

        self::assertTrue($container->has('foo'));
    }
}
