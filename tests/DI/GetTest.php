<?php

namespace DependencyInjector\Test\DI;

use DateTime;
use DependencyInjector\DI;
use DependencyInjector\Exception;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    /** Test that DI::get() throws a exception when the requested dependency is unknown.
     * @test */
    public function throwsForUnknownDependencies()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown dependency 'unknown'");

        DI::get('unknown');
    }

    /** Test that DI::get() executes the given function and returns the return value.
     * @test */
    public function executesAnonymousFunctions()
    {
        DI::set('anonymous', function () {
            return 'fooBar';
        });

        $result = DI::get('anonymous');

        self::assertSame('fooBar', $result);
    }

    /** @test */
    public function executesCallable()
    {
        DI::set('getter', [$this, 'getDependencyExample']);

        $result = DI::get('getter');

        self::assertSame('fooBar', $result);
    }

    public function getDependencyExample()
    {
        return 'fooBar';
    }

    /** Test that the function got executed only once.
     * @test */
    public function executesOnce()
    {
        $calls = 0;
        DI::set('callOnce', function () use (&$calls) {
            $calls = $calls + 1;
            return new DateTime();
        });

        DI::get('callOnce');
        DI::get('callOnce');

        self::assertSame(1, $calls);
    }

    /** Test that a non singleton got executed for each get.
     * @test */
    public function executesNonSingleton()
    {
        $calls = 0;
        DI::set('callTwice', function () use (&$calls) {
            $calls = $calls + 1;
            return new DateTime();
        }, false);

        DI::get('callTwice');
        DI::get('callTwice');

        self::assertSame(2, $calls);
    }

    /** Test that you can get instances with magic method.
     * @test */
    public function callStatic()
    {
        DI::set('magicCall', true);

        /** @noinspection PhpUndefinedMethodInspection */
        $result = DI::magicCall();

        self::assertTrue($result);
    }

    /** Test that undefined dependencies return the class name if it exists.
     * @test */
    public function returnsClassName()
    {
        $result = DI::get(__CLASS__);

        self::assertSame(__CLASS__, $result);
    }

    /** Test that a class name has to be case sensitive.
     * @test */
    public function classNameIsCaseSensitive()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown dependency '" . strtolower(__CLASS__) . "'");

        $result = DI::get(strtolower(__CLASS__));

        self::assertNotSame(strtolower(__CLASS__), $result);
    }

    /** Test that you can override class names.
     * @test */
    public function returnsStored()
    {
        DI::set(__CLASS__, 'FooBar');

        $result = DI::get(__CLASS__);

        self::assertSame('FooBar', $result);
    }
}
