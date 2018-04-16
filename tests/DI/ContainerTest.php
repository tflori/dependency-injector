<?php

namespace DependencyInjector\Test\DI;

use DependencyInjector\Container;
use DependencyInjector\DI;
use DependencyInjector\Test\Examples\TestableDI;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    protected $container;
    protected function setUp()
    {
        $this->container = m::mock(Container::class);
    }

    /** @test */
    public function createsAContainer()
    {
        $container = TestableDI::getContainer();

        self::assertInstanceOf(Container::class, $container);
    }

    /** @test */
    public function storesAnInstance()
    {
        DI::setContainer($this->container);

        self::assertSame($this->container, TestableDI::getContainer());
    }

    /** @test */
    public function resetsTheContainer()
    {
        DI::setContainer($this->container);

        DI::reset();

        self::assertNotSame($this->container, TestableDI::getContainer());
        self::assertInstanceOf(Container::class, TestableDI::getContainer());
    }
}
