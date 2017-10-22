<?php

namespace DependencyInjector\Test\example\DataProvider;

use DependencyInjector\DI;

class DatabaseObject
{
    /**
     * @var \PDO
     */
    private static $connection;

    /**
     * Execute a query and return the result set.
     *
     * @param string $sql
     * @return array
     * @throws \Exception
     */
    public function query($sql)
    {
        $connection = self::getConnection();
        $result = $connection->query($sql);

        if (!$result) {
            throw new \Exception('SQL error');
        }

        return $result->fetchAll();
    }

    /**
     * @return \PDO
     */
    private static function getConnection()
    {
        if (!self::$connection) {
            self::$connection = new \PDO('mysql:host=localhost;dbname=anything');
        }

        return self::$connection;
    }
}

// has to be added (maybe at config stage or bootstrap)
//DI::set('database', function() { return new DatabaseObject(); }, false);
