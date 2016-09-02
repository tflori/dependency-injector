<?php

/**
 * Class MySingleton
 *
 * Example of a singleton pattern.
 */
class MySingleton {
    /** @var MySingleton */
    private static $_instance;

    /**
     * @return MySingleton
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Example function of the singleton. It Could also be any other result (for example a Database result).
     *
     * @return string
     */
    public function getResult() {
        return 'defaultResult';
    }

    private function __construct() {}
    private function __clone() {}
}
