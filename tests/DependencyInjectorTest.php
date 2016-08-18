<?php

use PHPUnit\Framework\TestCase;
use DependencyInjector as DI;

class DependencyInjectorTest extends TestCase {
    /**
     * Test that the setup works - the class should exist
     */
    public function testSetUp() {
        $this->assertTrue(class_exists('DependencyInjector'));
    }

    /**
     * Test that DI::get() throws a exception when the requested depency is unknown.
     *
     * @expectedException DependencyInjectorException
     * @expectedExceptionMessage Unknown dependency 'unknown'
     */
    public function testGet_throwsForUnknownDependencies() {
        DI::get('unknown');
    }
}
