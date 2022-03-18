<?php

namespace DependencyInjector\Test\ClassFactory;

use DependencyInjector\Container;
use DependencyInjector\Factory\ClassFactory;
use DependencyInjector\Factory\StringArgument;
use DependencyInjector\Test\Examples\AnotherService;
use DependencyInjector\Test\Examples\SomeService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Mockery\Mock;

class MethodCallTest extends MockeryTestCase
{
    /** @var Container|Mock */
    protected $container;

    protected function setUp(): void
    {
        $this->container = m::mock(Container::class);
    }

    /** @test */
    public function callsMethodsWithStaticArguments()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService());
        $dt = new \DateTime();

        $factory->addMethodCall('methodA', true, 42, $dt);

        /** @var AnotherService $instance */
        $instance = $factory->getInstance();

        self::assertSame([
            'calls' => 1,
            'args' => [[true, 42, $dt]],
        ], $instance->calls['methodA']);
    }

    /** @test */
    public function resolvesStringsFromContainer()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService());
        $db = m::mock('PDO');

        $this->container->shouldReceive('has')
            ->with('db')
            ->once()->andReturn(true);
        $this->container->shouldReceive('get')
            ->with('db')
            ->once()->andReturn($db);

        $factory->addMethodCall('setDb', 'db');
        /** @var AnotherService $instance */
        $instance = $factory->getInstance();

        self::assertSame([
            'calls' => 1,
            'args' => [[$db]],
        ], $instance->calls['setDb']);
    }

    /** @test */
    public function allowsMultipleCalls()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService());
        $this->container->makePartial();

        $factory->addMethodCall('option', 'optionA', 'valueA');
        $factory->addMethodCall('option', 'optionB', 'valueB');
        /** @var AnotherService $instance */
        $instance = $factory->getInstance();

        self::assertSame([
            'calls' => 2,
            'args' => [
                ['optionA', 'valueA'],
                ['optionB', 'valueB'],
            ],
        ], $instance->calls['option']);
    }

    /** @test */
    public function stringsCanBePassedAsStringArgument()
    {
        $factory = new ClassFactory($this->container, AnotherService::class);
        $factory->addArguments(new SomeService());

        $this->container->shouldNotReceive('has');

        $factory->addMethodCall('foo', new StringArgument('db'));
        /** @var AnotherService $instance */
        $instance = $factory->getInstance();

        self::assertSame([
            'calls' => 1,
            'args' => [['db']],
        ], $instance->calls['foo']);
    }
}
