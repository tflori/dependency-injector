<?php

namespace DepenencyInjector\Test\DI;

use DependencyInjector\DI;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CommonTest extends TestCase
{

    public function tearDown()
    {
        DI::reset();
        parent::tearDown();
    }

    /** Test that the setup works - the class should exist
     * @test */
    public function isDefined()
    {
        self::assertTrue(class_exists('DependencyInjector\DI'));
    }

    /** Test that constructor is not callable
     * @test */
    public function privateConstructor()
    {
        $refDI        = new ReflectionClass(DI::class);
        $refConstruct = $refDI->getMethod('__construct');

        self::assertTrue($refConstruct->isPrivate());
    }

    /** Test that DI::reset() resets the DependencyInjector.
     * @test */
    public function reset()
    {
        DI::set(__CLASS__, function () {
            return 'FooBar';
        });
        DI::get(__CLASS__);

        DI::reset();

        self::assertSame(__CLASS__, DI::get(__CLASS__));
    }

     /** @test */
    public function has()
    {
        DI::set('foo', 'bar');

        self::assertTrue(DI::has('foo'));
    }

     /** @test */
    public function delete()
    {
        DI::set('foo', function () {
            return 'bar';
        });
        DI::get('foo');

        DI::delete('foo');

        self::assertFalse(DI::has('foo'));
    }

    /** @test */
    public function allowsAliasesForValues()
    {
        DI::set('password', 's3cr3t');

        DI::alias('password', 'pw');
        DI::set('password', '4n0th3r s3cr3t');

        self::assertSame('4n0th3r s3cr3t', DI::get('pw'));
    }

    /** @test */
    public function resetsAliasWhenRegisterDependency()
    {
        DI::set('password', 's3cr3t');
        DI::alias('password', 'pw');

        DI::set('pw', 'parent worker');

        self::assertSame('parent worker', DI::get('pw'));
    }
}
