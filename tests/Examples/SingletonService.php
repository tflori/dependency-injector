<?php

namespace DependencyInjector\Test\Examples;

class SingletonService
{
    /** @var SingletonService */
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
