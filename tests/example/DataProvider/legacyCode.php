<?php

use DependencyInjector\DI;

require_once __DIR__ . '/memcache.php';

class DataProvider {
    public function getSomeData() {
        $key = "SomeMemcacheKey";

        // before it was untestable
        // $cache = getMemcache();

        /** @var Memcached $cache */
        $cache = DI::get('memcache');

        $results = $cache->get($key);
        if (!$results) {

            // before it was untestable
            // $database = new DatabaseObject();

            /** @var DatabaseObject $database */
            $database = DI::get('database');

            $sql = "SELECT * FROM someDatabase.someTable";
            $results = $database->query($sql);

            $cache->set($key, $results);
        }

        return $results;
    }
}
