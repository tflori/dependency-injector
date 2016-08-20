<?php

use DependencyInjector\DI;

require_once __DIR__ . '/MySingleton.php';

class MySingletonTest extends PHPUnit_Framework_TestCase {
    public function testClassExists() {
        self::assertTrue(class_exists('MySingleton'));
    }

    /**
     * Test the default behaviour of your code.
     */
    public function testGetAnObject() {
        /** @var $mySingleton MySingleton */
        $mySingleton = (DI::get(MySingleton::class))::getInstance();

        self::assertTrue($mySingleton instanceof MySingleton);
        self::assertSame('defaultResult', $mySingleton->getResult());
    }

    /**
     * Test to mock your singleton.
     */
    public function testGetAMockObject() {
        // prepare the mock
        $mock = Mockery::mock(MySingleton::class);
        $mock->shouldReceive('getInstance')->andReturnSelf();
        DI::set(MySingleton::class, $mock);

        // assign
        $mock->shouldReceive('getResult')->andReturn('differentResult');

        // act like your would usually act in your app
        /** @var $mySingleton MySingleton */
        $mySingleton = (DI::get(MySingleton::class))::getInstance();

        // assert different result
        self::assertTrue($mySingleton instanceof MySingleton);
        self::assertSame('differentResult', $mySingleton->getResult());
    }
}
