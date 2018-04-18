<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\NotFoundExceptionInterface;

class RetrieveTest extends MockeryTestCase
{
    /** @test */
    public function hasReturnsFalseWhenNameIsUnknown()
    {
        $container = new Container();

        self::assertFalse($container->has('foo'));
    }

    /** @test */
    public function getThrowsWhenNameIsUnknown()
    {
        $container = new Container();

        self::expectException(NotFoundExceptionInterface::class);
        self::expectExceptionMessage('Name foo could not be resolved');

        $container->get('foo');
    }

    /** @test */
    public function hasReturnsTrueForStoredInstance()
    {
        $container = new Container();
        $container->instance('foo', 42);

        self::assertTrue($container->has('foo'));
    }

    /** @test */
    public function hasReturnsTrueForRegisteredAliases()
    {
        $container = new Container();
        $container->instance('foo', 42);
        $container->alias('foo', 'bar');

        self::assertTrue($container->has('bar'));
    }
}
