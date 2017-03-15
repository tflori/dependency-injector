<?php

use DependencyInjector\DI;

require_once __DIR__ . '/MySingleton.php';

/**
 * It's just a simple function that is using MySingleton static method.
 *
 * @return string
 */
function getTheSingletonResult() {

    // before it was untestable
    // $singleton = MySingleton::getInstance();

    /** @var $singleton MySingleton */
    $class = DI::get('MySingleton');
    $singleton = $class::getInstance();

    return $singleton->getResult();
}

/**
 * Class SingletonUser
 *
 * Uses the singleton to get a result.
 */
class SingletonUser {

    /**
     * This method shows a second way how to rewrite existing code to be testable. But remember that you have to
     * set up the dependency before using it (for example in bootstrap).
     *
     * Example:
     * DI::set('mySingleton', function() { return MySingleton::getInstance(); });
     *
     * @return mixed
     */
    public function getResult() {

        // before it was untestable
        // return MySingleton::getInstance()->getResult();

        return DI::get('mySingleton')->getResult();
    }
}
