<?php

use PHPUnit\Framework\TestCase;
use DependencyInjector\DI;

class DependencyInjectorTest extends TestCase {

    public function tearDown() {
        DI::reset();
        parent::tearDown();
    }

    /**
     * Test that the setup works - the class should exist
     */
    public function testSetUp() {
        $this->assertTrue(class_exists('DependencyInjector\DI'));
    }

    /**
     * Test that constructor is not callable
     */
    public function testPrivateConstructor() {
        $refDI = new ReflectionClass(DI::class);
        $refConstruct = $refDI->getMethod('__construct');

        $this->assertTrue($refConstruct->isPrivate());
    }

    /**
     * Test that DI::get() throws a exception when the requested dependency is unknown.
     */
    public function testGet_throwsForUnknownDependencies() {
        $this->expectException(DependencyInjector\Exception::class);
        $this->expectExceptionMessage("Unknown dependency 'unknown'");

        DI::get('unknown');
    }

    /**
     * Test that DI::set() stores something.
     */
    public function testSet_storesSomething() {
        $something = [$this, __FUNCTION__];

        DI::set('something', $something);

        $this->assertEquals($something, DI::get('something'));
    }

    /**
     * Test that DI::get() executes the given function and returns the return value.
     */
    public function testGet_executesAnonymousFunctions() {
        DI::set('anonymous', function() {
            return 'fooBar';
        });

        $result = DI::get('anonymous');

        $this->assertSame('fooBar', $result);
    }

    /**
     * Test that the function got not executed before get.
     */
    public function testSet_doesNotExecute() {
        $calls = 0;

        DI::set('dontCall', function() use(&$calls) {
            $calls = $calls + 1;
        });

        $this->assertSame(0, $calls);
    }

    /**
     * Test that the function got executed only once.
     */
    public function testGet_executesOnce() {
        $calls = 0;
        DI::set('callOnce', function() use(&$calls) {
            $calls = $calls + 1;
            return new DateTime();
        });

        DI::get('callOnce');
        DI::get('callOnce');

        $this->assertSame(1, $calls);
    }

    /**
     * Test that a non singleton got executed for each get.
     */
    public function testGet_executesNonSingleton() {
        $calls = 0;
        DI::set('callTwice', function() use(&$calls) {
            $calls = $calls + 1;
            return new DateTime();
        }, false);

        DI::get('callTwice');
        DI::get('callTwice');

        $this->assertSame(2, $calls);
    }

    /**
     * Test that DI::set() overrides created instances.
     */
    public function testSet_overridesInstances() {
        DI::set('microtime', function() {
            return microtime(true);
        });
        $result1 = DI::get('microtime');

        DI::set('microtime', function() {
            return microtime(true);
        });
        $result2 = DI::get('microtime');

        $this->assertNotSame($result1, $result2);
    }

    /**
     * Test that you can get instances with magic method.
     */
    public function testGet_callStatic() {
        DI::set('magicCall', true);

        /** @noinspection PhpUndefinedMethodInspection */
        $result = DI::magicCall();

        $this->assertTrue($result);
    }

    /**
     * Test that undefined dependencies return the class name if it exists.
     */
    public function testGet_returnsClassName() {
        $result = DI::get(__CLASS__);

        $this->assertSame(__CLASS__, $result);
    }

    /**
     * Test that you can override class names.
     */
    public function testGet_returnsStored() {
        DI::set(__CLASS__, 'FooBar');

        $result = DI::get(__CLASS__);

        $this->assertSame('FooBar', $result);
    }

    /**
     * Test that DI::reset() resets the DependencyInjector.
     */
    public function testReset() {
        DI::set(__CLASS__, function() {
            return 'FooBar';
        });
        DI::get(__CLASS__);

        DI::reset();

        $this->assertSame(__CLASS__, DI::get(__CLASS__));
    }
}
