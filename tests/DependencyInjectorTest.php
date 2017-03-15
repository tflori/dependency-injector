<?php

use PHPUnit\Framework\TestCase;
use DependencyInjector\DI;

class DependencyInjectorTest extends TestCase
{

    public function tearDown()
    {
        DI::reset();
        parent::tearDown();
    }

    /**
     * Test that the setup works - the class should exist
     */
    public function testSetUp()
    {
        self::assertTrue(class_exists('DependencyInjector\DI'));
    }

    /**
     * Test that constructor is not callable
     */
    public function testPrivateConstructor()
    {
        $refDI        = new ReflectionClass(DI::class);
        $refConstruct = $refDI->getMethod('__construct');

        self::assertTrue($refConstruct->isPrivate());
    }

    /**
     * Test that DI::get() throws a exception when the requested dependency is unknown.
     */
    public function testGetThrowsForUnknownDependencies()
    {
        $this->expectException(DependencyInjector\Exception::class);
        $this->expectExceptionMessage("Unknown dependency 'unknown'");

        DI::get('unknown');
    }

    /**
     * Test that DI::set() stores something.
     */
    public function testSetStoresSomethingNotCallable()
    {
        $something = [$this, 'nonExistingMethod'];

        DI::set('something', $something);

        self::assertEquals($something, DI::get('something'));
    }

    /**
     * Test that DI::get() executes the given function and returns the return value.
     */
    public function testGetExecutesAnonymousFunctions()
    {
        DI::set('anonymous', function () {
            return 'fooBar';
        });

        $result = DI::get('anonymous');

        self::assertSame('fooBar', $result);
    }

    public function testGetExecutesCallable()
    {
        DI::set('getter', [$this, 'getDependencyExample']);

        $result = DI::get('getter');

        self::assertSame('fooBar', $result);
    }

    public function getDependencyExample()
    {
        return 'fooBar';
    }

    public function testSetStoresValue()
    {
        $something = [$this, 'getDependencyExample'];
        DI::set('array', $something, false, true);

        $result = DI::get('array');

        self::assertSame($something, $result);
    }

    /**
     * Test that the function got not executed before get.
     */
    public function testSetDoesNotExecute()
    {
        $calls = 0;

        DI::set('dontCall', function () use (&$calls) {
            $calls = $calls + 1;
        });

        self::assertSame(0, $calls);
    }

    /**
     * Test that the function got executed only once.
     */
    public function testGet_executesOnce()
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

    /**
     * Test that a non singleton got executed for each get.
     */
    public function testGet_executesNonSingleton()
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

    /**
     * Test that DI::set() overrides created instances.
     */
    public function testSet_overridesInstances()
    {
        DI::set('microtime', function () {
            return microtime(true);
        });
        $result1 = DI::get('microtime');

        DI::set('microtime', function () {
            return microtime(true);
        });
        $result2 = DI::get('microtime');

        self::assertNotSame($result1, $result2);
    }

    /**
     * Test that you can get instances with magic method.
     */
    public function testGet_callStatic()
    {
        DI::set('magicCall', true);

        /** @noinspection PhpUndefinedMethodInspection */
        $result = DI::magicCall();

        self::assertTrue($result);
    }

    /**
     * Test that undefined dependencies return the class name if it exists.
     */
    public function testGet_returnsClassName()
    {
        $result = DI::get(__CLASS__);
    }

    /**
     * Test that a class name has to be case sensitive.
     */
    public function testGet_classNameIsCaseSensitive()
    {
        $this->expectException(DependencyInjector\Exception::class);
        $this->expectExceptionMessage("Unknown dependency '" . strtolower(__CLASS__) . "'");

        $result = DI::get(strtolower(__CLASS__));

        self::assertNotSame(strtolower(__CLASS__), $result);
    }

    /**
     * Test that you can override class names.
     */
    public function testGet_returnsStored()
    {
        DI::set(__CLASS__, 'FooBar');

        $result = DI::get(__CLASS__);

        self::assertSame('FooBar', $result);
    }

    /**
     * Test that DI::reset() resets the DependencyInjector.
     */
    public function testReset()
    {
        DI::set(__CLASS__, function () {
            return 'FooBar';
        });
        DI::get(__CLASS__);

        DI::reset();

        self::assertSame(__CLASS__, DI::get(__CLASS__));
    }

    public function testHas()
    {
        DI::set('foo', 'bar');

        self::assertTrue(DI::has('foo'));
    }

    public function testDelete()
    {
        DI::set('foo', function () {
            return 'bar';
        });
        DI::get('foo');

        DI::delete('foo');

        self::assertFalse(DI::has('foo'));
    }
}
