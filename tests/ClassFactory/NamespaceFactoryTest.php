<?php

namespace DependencyInjector\Test\ClassFactory;

use DependencyInjector\Container;
use DependencyInjector\Factory\NamespaceFactory;
use DependencyInjector\Test\Examples\SomeService;
use Mockery as m;
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

    /** @test */
    public function canBeShared()
    {
        /** @var m\Mock|NamespaceFactory $factory */
        $factory = m::mock(NamespaceFactory::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $factory->shouldReceive('build')->with()->once()->andReturn((object)['foo' => 'bar']);

        $factory->share();
        $first = $factory->getInstance('FooBar');
        $second = $factory->getInstance('FooBar');

        self::assertSame($first, $second);
    }

    /** @test */
    public function sharedInstancesIdentifyByName()
    {
        /** @var m\Mock|NamespaceFactory $factory */
        $factory = m::mock(NamespaceFactory::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $factory->shouldReceive('build')->with()->twice()
            ->andReturn((object)['type' => 'first'], (object)['type' => 'second']);

        $factory->share();
        $first = $factory->getInstance('first');
        $second = $factory->getInstance('second');

        self::assertNotSame($first, $second);
    }
}
