<?php

use DependencyInjector\DI;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/legacyCode.php';

class MySingletonTest extends TestCase {

    /**
     * Test the default behaviour of your code.
     */
    public function testGetAnObject() {
        // act
        $result = getTheSingletonResult();

        //assert
        self::assertSame('defaultResult', $result);
    }

    /**
     * Test to mock your singleton.
     */
    public function testGetAMockObject() {
        // prepare the mock
        $mock = Mockery::mock(MySingleton::class);
        $mock->shouldIgnoreMissing();
        $mock->shouldReceive('getInstance')->andReturnSelf();
        DI::set(MySingleton::class, $mock);

        // assign
        $mock->shouldReceive('getResult')->once()->andReturn('differentResult');

        // act
        $result = getTheSingletonResult();

        // assert
        self::assertSame('differentResult', $result);
    }

    public function testGetResultFromMock() {
        // assign
        $mock = Mockery::mock(MySingleton::class);
        $mock->shouldReceive('getResult')->once()->andReturn('anotherResult');
        DI::set('mySingleton', $mock);

        // act
        $singletonUser = new SingletonUser();
        $result = $singletonUser->getResult();

        // assert
        self::assertSame('anotherResult', $result);
    }
}
