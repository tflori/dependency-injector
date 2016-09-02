<?php

// before
//function getMemcache() {
//    static $memcache;
//    if (!$memcache) {
//        $memcache = new Memcached();
//        $memcache->addServer('localhost', 11211);
//    }
//    return $memcache;
//}

// now

use DependencyInjector\DI;

DI::set('memcache', function() {
    $memcache = new Memcached();
    $memcache->addServer('localhost', 11211);
    return $memcache;
});

// keep the function for your not refactored lagacy code:
function getMemcache() {
    return DI::get('memcache');
}
