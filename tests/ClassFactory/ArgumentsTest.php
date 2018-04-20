<?php

namespace DependencyInjector\Test\ClassFactory;

use DependencyInjector\ClassFactory;
use DependencyInjector\Container;
use DependencyInjector\StringArgument;
use DependencyInjector\Test\Examples\AnotherService;
use DependencyInjector\Test\Examples\SomeService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use Mockery as m;

class BasicTest extends MockeryTestCase
{
    /** @var Container|Mock */
    protected $container;
    
    protected function setUp()
    {
        $this->container = m::mock(Container::class);
    }

    /** @test */
    public function passesArguments()
    {
        // another service requires some service
        $factory = new ClassFactory($this->container, AnotherService::class);
        $some = new SomeService();

        self::assertInstanceOf(AnotherService::class, $factory->build($some));
    }

    /** @test */
    public function providesArguments()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);

        $factory->addArguments(new SomeService());

        self::assertInstanceOf(AnotherService::class, $factory->build());
    }

    /** @test */
    public function resolvesStringsFromContainer()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(SomeService::class);

        $this->container->shouldReceive('has')
            ->with(SomeService::class)
            ->once()->andReturn(true);
        $this->container->shouldReceive('get')
            ->with(SomeService::class)
            ->once()->andReturn(new SomeService());

        self::assertInstanceOf(AnotherService::class, $factory->build());
    }

    /** @test */
    public function passesAdditionalArguments()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService());

        /** @var AnotherService $service */
        $service = $factory->build('foo', 'bar');

        self::assertSame(['foo', 'bar'], $service->args);
    }

    /** @test */
    public function stringArgumentsAreNotResolved()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService(), new StringArgument('foo bar'));

        /** @var AnotherService $service */
        $service = $factory->build();

        self::assertSame(['foo bar'], $service->args);
    }
}
