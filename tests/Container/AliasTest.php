<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use DependencyInjector\Exception;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AliasTest extends MockeryTestCase
{
    /** @test */
    public function storesAnAlias()
    {
        $container = new Container();
        $container->instance('foo', 42);

        $container->alias('foo', 'bar');

        self::assertSame(42, $container->get('bar'));
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
    public function throwsWhenOriginCannotBeResolved()
    {
        $container = new Container();

        self::expectException(Exception::class);
        self::expectExceptionMessage('Origin foo could not be resolved');

        $container->alias('foo', 'bar');
    }
}
