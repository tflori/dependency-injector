<?php

namespace DependencyInjector\Test\example\Singleton;

use DependencyInjector\DI;
use Mockery;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/legacyCode.php';

class MySingletonTest extends TestCase
{

    /** Test the default behaviour of your code.
     * @test */
    public function nonMockedUsage()
    {
        // act
        $result = getTheSingletonResult();

        //assert
        self::assertSame('defaultResult', $result);
    }

    /** Test to mock your singleton.
     * @test */
    public function mockedSingletonUsage()
    {
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

    /** Test to provide your singleton as dependency
     * @test */
    public function mockedDependencyUsage()
    {
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
