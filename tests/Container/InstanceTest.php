<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class InstanceTest extends MockeryTestCase
{
    /** @dataProvider provideValidValues
     * @param $name
     * @param $value
     * @test */
    public function storesAnything($name, $value)
    {
        $container = new Container();

        $container->instance($name, $value);

        self::assertSame($value, $container->get($name));
    }

    public function provideValidValues()
    {
        return [
            ['string', 'foo bar'],
            ['bool', false],
            ['int', 42],
            ['object', new \DateTime()],
            ['null', null],
            ['closure', function () {
            }]
        ];
    }

    /** @test */
    public function overwritesAnExistingAlias()
    {
        $container = new Container();
        $container->instance('foo', 42);
        $container->alias('foo', 'bar');

        $container->instance('bar', 23);

        self::assertSame(23, $container->get('bar'));
    }

    /** @test */
    public function setForwardsToInstance()
    {
        $container = new Container();
        $container->set('foo', function () {
            return 42;
        }, true, true);

        self::assertInstanceOf(\Closure::class, $container->get('foo'));
    }
}
