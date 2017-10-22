<?php

use DependencyInjector\DI;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/legacyCode.php';

class DataProviderTest extends TestCase {
    /**
     * @var DataProvider
     */
    protected $dataProvider;

    protected function setUp()
    {
        // to mock the database during tests
        DI::set('database', function () {
            $mock = Mockery::mock('DatabaseObject');
            $mock->shouldReceive('query')->andReturn([])->byDefault();
        });

        $this->dataProvider = new DataProvider;

        parent::setUp();
    }


    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function returnsCached()
    {
        $mock = Mockery::mock('memcache_class');
        $mock->shouldReceive('get')->once()->with('SomeMemcacheKey')->andReturn('anyResult');
        DI::set('memcache', $mock);

        $result = $this->dataProvider->getSomeData();

        self::assertSame('anyResult', $result);
    }

    /** @test */
    public function queriesDatabase()
    {
        $memcache = Mockery::mock('memcache_class');
        $memcache->shouldReceive('get')->andReturn(null);
        $memcache->shouldIgnoreMissing();
        DI::set('memcache', $memcache);

        $database = Mockery::mock(DatabaseObject::class);
        $database->shouldReceive('query')
            ->once()->with("SELECT * FROM someDatabase.someTable")
            ->andReturn('fooBar');
        DI::set('database', $database);

        $result = $this->dataProvider->getSomeData();

        self::assertSame('fooBar', $result);
    }
}
