<?php

use PHPUnit\Framework\TestCase;
use DependencyInjector\DI;

class DependencyInjectorTest extends TestCase {

    /**
     * Test that the setup works - the class should exist
     */
    public function testSetUp() {
        $this->assertTrue(class_exists('DependencyInjector\DI'));
    }

    /**
     * Test that DI::get() throws a exception when the requested dependency is unknown.
     */
    public function testGet_throwsForUnknownDependencies() {
        $this->expectException(DependencyInjector\Exception::class);
        $this->expectExceptionMessage("Unknown dependency 'unknown'");

        DI::get('unknown');
    }
}
