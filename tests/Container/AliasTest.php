<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use DependencyInjector\Exception;
use DependencyInjector\Test\Examples;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AliasTest extends MockeryTestCase
{
    /** @test */
    public function storesAnAliasForInstance()
    {
        $container = new Container();
        $container->instance('foo', 42);

        $container->alias('foo', 'bar');

        self::assertTrue($container->has('bar'));
        self::assertSame(42, $container->get('bar'));
    }

    /** @test */
    public function storesAnAliasForFactory()
    {
        $container = new Container();
        $container->registerNamespace(Examples::class, 'Factory');

        $container->alias('dateTime', 'foo');

        self::assertTrue($container->has('foo'));
        self::assertInstanceOf(\DateTime::class, $container->get('foo'));
    }

    /** @test */
    public function throwsExceptionWhenInstanceExists()
    {
        $container = new Container();
        $container->instance('foo', 'John Doe');
        $container->instance('bar', 'Jane Doe');

        self::expectException(Exception::class);
        self::expectExceptionMessage('Instance for bar already exists');

        $container->alias('foo', 'bar');
    }

    /** @test */
    public function throwsExceptionWhenFactoryExists()
    {
        $container = new Container();
        $container->instance('foo', 'John Doe');
        $container->registerNamespace(Examples::class, 'Factory');

        self::expectException(Exception::class);
        self::expectExceptionMessage('Factory for dateTime already exists');

        $container->alias('foo', 'dateTime');
    }

    /** @test */
    public function throwsWhenOriginCannotBeResolved()
    {
        $container = new Container();

        self::expectException(Exception::class);
        self::expectExceptionMessage('Origin foo could not be resolved');

        $container->alias('foo', 'bar');
    }
}
