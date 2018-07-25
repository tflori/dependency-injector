<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class MakeTest extends MockeryTestCase
{
    /** @test */
    public function returnsDefinedInstance()
    {
        $instance = new \SimpleXMLElement('<d/>');
        $container = new Container();
        $container->instance(\SimpleXMLElement::class, $instance);

        $result = $container->make(\SimpleXMLElement::class, '<f/>');

        self::assertSame($instance, $result);
    }

    /** @test */
    public function createsANewInstance()
    {
        $container = new Container();

        $result = $container->make(\SimpleXMLElement::class, '<f/>');

        self::assertEquals(new \SimpleXMLElement('<f/>'), $result);
    }
}
