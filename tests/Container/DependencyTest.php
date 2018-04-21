<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\CallableFactory;
use DependencyInjector\ClassFactory;
use DependencyInjector\Container;
use DependencyInjector\Exception;
use DependencyInjector\SingletonFactory;
use DependencyInjector\Test\Examples\DateTimeFactory;
use DependencyInjector\Test\Examples\SingletonService;
use DependencyInjector\Test\Examples\SomeService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class DependencyTest extends MockeryTestCase
{
    /** @test */
    public function createsACallableFactory()
    {
        $container = new Container();

        self::assertInstanceOf(CallableFactory::class, $container->add('foo', function () {
            return 42;
        }));
    }

    /** @test */
    public function createsTheFactory()
    {
        $container = new Container();

        self::assertInstanceOf(DateTimeFactory::class, $container->add('dt', DateTimeFactory::class));
    }

    /** @test */
    public function returnsTheFactoryItself()
    {
        $container = new Container();
        $factory = new DateTimeFactory($container);

        self::assertSame($factory, $container->add('dt', $factory));
    }

    /** @test */
    public function returnsASingletonFactory()
    {
        $container = new Container();

        self::assertInstanceOf(SingletonFactory::class, $container->add('singleton', SingletonService::class));
    }

    /** @test */
    public function returnsAClassFactory()
    {
        $container = new Container();

        self::assertInstanceOf(ClassFactory::class, $container->add('service', SomeService::class));
    }

    /** @test */
    public function resolvesUsingTheCallback()
    {
        $container = new Container();
        $container->add('foo', function () {
            return 42;
        });

        self::assertSame(42, $container->get('foo'));
    }

    /** @test */
    public function resolvesUsingFactory()
    {
        $container = new Container();
        $container->add('dt', DateTimeFactory::class);

        self::assertInstanceOf(\DateTime::class, $container->get('dt'));
    }

    /** @test */
    public function resolvesUsingTheFactory()
    {
        $container = new Container();
        $factory = m::mock(DateTimeFactory::class, [$container])->makePartial();
        $container->add('dt', $factory);

        $factory->shouldReceive('build')->once()->andReturn(new \DateTime());

        $container->get('dt');
    }

    /** @test */
    public function resolvesUsingSingletonFactory()
    {
        $container = new Container();
        $container->add('singleton', SingletonService::class);

        self::assertInstanceOf(SingletonService::class, $container->get('singleton'));
    }

    /** @test */
    public function resolvesUsingClassFactory()
    {
        $container = new Container();
        $container->add('service', SomeService::class);

        self::assertInstanceOf(SomeService::class, $container->get('service'));
    }

    /** @test */
    public function throwsOtherwise()
    {
        $container = new Container();

        self::expectException(Exception::class);
        self::expectExceptionMessage('$getter is invalid for dependency. Maybe you want to add an instance instead?');

        $container->add('foo', 'bar');
    }

    /** @test */
    public function overwritesExistingAlias()
    {
        $container = new Container();
        $container->instance('foo', 'bar');
        $container->alias('foo', 'service');

        $container->add('service', SomeService::class);

        self::assertInstanceOf(SomeService::class, $container->get('service'));
    }

    /** @test */
    public function overwritesExistingInstances()
    {
        $container = new Container();
        $container->instance('service', 'foo bar');

        $container->add('service', SomeService::class);

        self::assertInstanceOf(SomeService::class, $container->get('service'));
    }

    /** @test */
    public function shareCallsShareOnTheFactory()
    {
        $container = new Container();
        $factory = m::mock(DateTimeFactory::class);

        $factory->shouldReceive('share')->once();

        $container->share('dt', $factory);
    }

    /** @test */
    public function shareUsesAddAndReturnsFactory()
    {
        $container = m::mock(Container::class)->makePartial();

        $container->shouldReceive('add')
            ->with('dt', DateTimeFactory::class)
            ->once()->andReturn(new DateTimeFactory($container));

        $factory = $container->share('dt', DateTimeFactory::class);

        self::assertInstanceOf(DateTimeFactory::class, $factory);
    }
}
