<?php

namespace DependencyInjector\Test\Container;

use DependencyInjector\Container;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use DependencyInjector\Test\Examples;

class NamespaceTest extends MockeryTestCase
{
    /** @test */
    public function looksForDependenciesInRegisteredNamespace()
    {
        $container = new Container();

        $container->registerNamespace(Examples\NS1::class);

        self::assertTrue($container->has('Faker'));
    }

    /** @test */
    public function usesSuffixForClassNameIfProvided()
    {
        $container = new Container();

        $container->registerNamespace(Examples::class, 'Factory');

        self::assertTrue($container->has('DateTime'));
    }

    /** @test */
    public function removesTrailingBackslashes()
    {
        $container = new Container();

        $container->registerNamespace('DependencyInjector\Test\Examples\\', 'Factory');

        self::assertTrue($container->has('DateTime'));
    }

    /** @test */
    public function doesNotUseNonFactories()
    {
        $container = new Container();

        $container->registerNamespace(Examples::class);

        self::assertFalse($container->has('SomeService'));
        self::assertFalse($container->has('AnotherService'));
    }

    /** @test */
    public function resolvesADependency()
    {
        $container = new Container();

        $container->registerNamespace(Examples::class, 'Factory');
        $dt = $container->get('DateTime', '2018-01-01');

        self::assertInstanceOf(\DateTime::class, $dt);
        self::assertEquals(new \DateTime('2018-01-01'), $dt);
    }

    /** @test */
    public function usesLastInFirstOutPrinciple()
    {
        $container = new Container();

        $container->registerNamespace(Examples\NS1::class);
        $container->registerNamespace(Examples\NS2::class);

        self::assertInstanceOf(\DOMDocument::class, $container->get('faker'));
    }

    /** @test */
    public function registerAgainToHaveNamespaceUsedFirst()
    {
        $container = new Container();

        $container->registerNamespace(Examples\NS1::class);
        $container->registerNamespace(Examples\NS2::class);
        $container->registerNamespace(Examples\NS2::class);
        $container->registerNamespace(Examples\NS1::class);

        self::assertInstanceOf(\SimpleXMLElement::class, $container->get('faker'));
    }
}
