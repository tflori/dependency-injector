<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use DependencyInjector\Factory\NamespaceFactory;
use DependencyInjector\NotFoundException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PatternFactoryTest extends MockeryTestCase
{
    /** @test */
    public function checksIfPatternFactoryMatches()
    {
        /** @var m\Mock|NamespaceFactory $factory */
        $factory = m::mock(NamespaceFactory::class);
        $factory->shouldReceive('matches')->with('FooBar')
            ->once()->andReturnFalse();
        $container = new Container();
        $container->add('', $factory);

        self::expectException(NotFoundException::class);

        $container->get('FooBar');
    }

    /** @test */
    public function getsInstanceFromPatternFactory()
    {
        /** @var m\Mock|NamespaceFactory $factory */
        $factory = m::mock(NamespaceFactory::class);
        $factory->shouldReceive('matches')->with('FooBar')
            ->once()->andReturnTrue();
        $factory->shouldReceive('getInstance')->with('FooBar')
            ->once()->andReturn('fooBar');
        $container = new Container();
        $container->addPatternFactory($factory);

        $result = $container->get('FooBar');

        self::assertSame('fooBar', $result);
    }
}
