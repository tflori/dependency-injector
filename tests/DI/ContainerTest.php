<?php

namespace DependencyInjector\Test\DI;

use DependencyInjector\Container;
use DependencyInjector\DI;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ContainerTest extends MockeryTestCase
{
    /** @var Container|m\Mock */
    protected $container;

    protected function setUp(): void
    {
        $this->container = m::mock(Container::class);
    }

    protected function tearDown(): void
    {
        DI::reset();
    }

    /** @test */
    public function createsAContainer()
    {
        $container = DI::getContainer();

        self::assertInstanceOf(Container::class, $container);
    }

    /** @test */
    public function storesAnInstance()
    {
        DI::setContainer($this->container);

        self::assertSame($this->container, DI::getContainer());
    }

    /** @test */
    public function resetsTheContainer()
    {
        DI::setContainer($this->container);

        DI::reset();

        self::assertNotSame($this->container, DI::getContainer());
        self::assertInstanceOf(Container::class, DI::getContainer());
    }

    /** @test */
    public function delegatesGet()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('get')
            ->with('foo')
            ->once()->andReturn('bar');

        self::assertSame('bar', DI::get('foo'));
    }

    /** @test */
    public function delegatesGetWithArguments()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('get')
            ->with('foo', 'bar', 'baz')
            ->once()->andReturn(42);

        self::assertSame(42, DI::get('foo', 'bar', 'baz'));
    }

    /** @test */
    public function delegatesHas()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('has')
            ->with('foo')
            ->once()->andReturn(true);

        self::assertTrue(DI::has('foo'));
    }

    /** @test */
    public function delegatesRegisterNamespace()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('registerNamespace')
            ->with('Foo\Namespace')
            ->once();

        DI::registerNamespace('Foo\Namespace');
    }

    /** @test */
    public function delegatesAlias()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('alias')
            ->with('foo', 'bar')
            ->once();

        DI::alias('foo', 'bar');
    }

    /** @test */
    public function delegatesDelete()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('delete')
            ->with('foo')
            ->once();

        DI::delete('foo');
    }

    /** @test */
    public function delegatesInstance()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('instance')
            ->with('foo', 'bar')
            ->once();

        DI::instance('foo', 'bar');
    }

    /** @test */
    public function delegatesShare()
    {
        DI::setContainer($this->container);
        $factory = function () {
        };

        $this->container->shouldReceive('share')
            ->with('foo', $factory)
            ->once();

        DI::share('foo', $factory);
    }

    /** @test */
    public function delegatesAdd()
    {
        DI::setContainer($this->container);
        $factory = function () {
        };

        $this->container->shouldReceive('add')
            ->with('foo', $factory)
            ->once();

        DI::add('foo', $factory);
    }

    /** @test */
    public function delegatesCallsToGet()
    {
        DI::setContainer($this->container);

        $this->container->shouldReceive('get')
            ->with('foo')
            ->once()->andReturn('bar');

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame('bar', DI::foo());
    }
}
