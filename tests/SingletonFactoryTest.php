<?php

namespace DependencyInjector\Test;

use DependencyInjector\Container;
use DependencyInjector\Factory\SingletonFactory;
use DependencyInjector\Test\Examples\AdvancedSingleton;
use DependencyInjector\Test\Examples\SingletonService;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SingletonFactoryTest extends MockeryTestCase
{
    /** @test */
    public function callsGetInstance()
    {
        $factory = new SingletonFactory(new Container(), SingletonService::class);

        $singleton = $factory->getInstance();

        self::assertInstanceOf(SingletonService::class, $singleton);
    }

    /** @test */
    public function callsGetInstanceWithParameter()
    {
        $factory = new SingletonFactory(new Container(), AdvancedSingleton::class);

        /** @var AdvancedSingleton $singletonA */
        $singletonA = $factory->getInstance('a');
        /** @var AdvancedSingleton $singletonB */
        $singletonB = $factory->getInstance('b');

        self::assertNotSame($singletonA, $singletonB);
        self::assertSame('a', $singletonA->name);
        self::assertSame('b', $singletonB->name);
    }
}
