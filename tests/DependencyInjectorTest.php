<?php

use PHPUnit\Framework\TestCase;

class DependencyInjectorTest extends TestCase {
    public function testSetUp() {
        $this->assertTrue(class_exists('DependencyInjector'));
    }
}
