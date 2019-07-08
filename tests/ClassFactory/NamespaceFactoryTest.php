<?php

namespace DependencyInjector\Test\ClassFactory;

use DependencyInjector\Container;
use DependencyInjector\Factory\NamespaceFactory;
use DependencyInjector\Test\Examples\SomeService;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class NamespaceFactoryTest extends MockeryTestCase
{
    /** @test */
    public function matchesTheNamespace()
    {
        $factory = new NamespaceFactory(new Container(), \DependencyInjector\Test::class);

        $result = $factory->matches(self::class);

        self::assertTrue($result);
    }

    /** @test */
    public function buildsClass()
    {
        $factory = new NamespaceFactory(new Container(), \DependencyInjector\Test::class);

        $result = $factory->getInstance(SomeService::class);

        self::assertInstanceOf(SomeService::class, $result);
    }
}
