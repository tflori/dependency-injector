<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class MakeTest extends MockeryTestCase
{
    /** @test */
    public function returnsDefinedInstance()
    {
        $instance = new \DateTime('2018-01-01');
        $container = new Container();
        $container->instance(\DateTime::class, $instance);

        $result = $container->make(\DateTime::class);

        self::assertSame($instance, $result);
    }

    /** @test */
    public function createsANewInstance()
    {
        $container = new Container();

        $result = $container->make(\DateTime::class, '2018-01-01');

        self::assertEquals(new \DateTime('2018-01-01'), $result);
    }
}
