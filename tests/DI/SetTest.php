<?php

namespace DependencyInjector\Test\DI;

use DependencyInjector\Container;
use DependencyInjector\DI;
use DependencyInjector\Factory\CallableFactory;
use DependencyInjector\Factory\ClassFactory;
use DependencyInjector\Instance;
use DependencyInjector\Test\Examples\DateTimeFactory;
use DependencyInjector\Test\Examples\SomeService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SetTest extends MockeryTestCase
{
    /** @var Container|m\Mock */
    protected $container;

    protected function setUp()
    {
        $this->container = m::mock(Container::class);
    }

    protected function tearDown()
    {
        DI::reset();
    }

    /** @test */
    public function stringsAreInstances()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('instance')
            ->with('foo', 'bar')
            ->once()->andReturn(new Instance($this->container, 'bar'));

        DI::set('foo', 'bar');
    }

    /** @test */
    public function classNamesGetShared()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('share')
            ->with('service', SomeService::class)
            ->once()->andReturn(new ClassFactory($this->container, SomeService::class));

        DI::set('service', SomeService::class);
    }

    /** @test */
    public function factoriesGetShared()
    {
        DI::setContainer($this->container);
        $factory = new DateTimeFactory($this->container);

        $this->container->shouldReceive('share')
            ->with('dt', $factory)
            ->once()->andReturn($factory);

        DI::set('dt', $factory);
    }

    /** @test */
    public function callablesGetShared()
    {
        DI::setContainer($this->container);
        $closure = function () {
            return 23;
        };

        $this->container->shouldReceive('share')
            ->with('foo', $closure)
            ->once()->andReturn(new CallableFactory($this->container, $closure));

        DI::set('foo', $closure);
    }

    /** @test */
    public function sharingCanBeDisabled()
    {
        DI::setContainer($this->container);
        $factory = new DateTimeFactory($this->container);

        $this->container->shouldReceive('add')
            ->with('dt', $factory)
            ->once()->andReturn($factory);

        DI::set('dt', $factory, false);
    }

    /** @test */
    public function allowsArrayInSetAndReturnsFactories()
    {
        DI::setContainer($this->container);
        $instance1 = new Instance($this->container, '');
        $instance2 = new Instance($this->container, '');
        $factory = new DateTimeFactory($this->container);

        $this->container->shouldReceive('instance')
            ->with('foo', 42)
            ->once()->andReturn($instance1);
        $this->container->shouldReceive('instance')
            ->with('bar', m::type(\Closure::class))
            ->once()->andReturn($instance2);
        $this->container->shouldReceive('share')
            ->with('dt', DateTimeFactory::class)
            ->once()->andReturn($factory);

        $factories = DI::set([
            'foo' => 42,
            'bar' => [function () {
                return 23;
            }, true, true],
            'dt' => DateTimeFactory::class,
        ]);

        self::assertSame([
            'foo' => $instance1,
            'bar' => $instance2,
            'dt' => $factory,
        ], $factories);
    }
}
