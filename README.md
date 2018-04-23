# Dependency Injector

[![build status](https://gitlab.w00tserver.org/tflori/dependency-injector/badges/master/build.svg)](https://gitlab.w00tserver.org/tflori/dependency-injector/commits/master)
[![Coverage Status](https://coveralls.io/repos/github/tflori/dependency-injector/badge.svg?branch=master)](https://coveralls.io/github/tflori/dependency-injector?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/dependency-injector/v/stable)](https://packagist.org/packages/tflori/dependency-injector)
[![Total Downloads](https://poser.pugx.org/tflori/dependency-injector/downloads)](https://packagist.org/packages/tflori/dependency-injector)
[![License](https://poser.pugx.org/tflori/dependency-injector/license)](https://packagist.org/packages/tflori/dependency-injector)

A simple and lightweight dependency injector. Compatible to php standard recommendation for dependency injection 
containers ([PSR-11](https://www.php-fig.org/psr/psr-11/)).
 
## What is a dependency

Something that your application needs to work correct. For example an instance of `Calculator` or `Config`.

## Basic usage

One of the biggest problems in software development is the tight coupling of dependencies. Imagine a class that creates
a instance from `DatabaseConnection` with `new DatabaseConnection()`. This is called a tight coupling - it is so tight
that you would have to overwrite your autoloader (what is not always possible) to mock the database connection.

There are two solutions to test this class without the need to have a database connection during the tests.

### Passing the dependency

When creating an object of that class you can provide the `DatabaseConnection` to the constructor. For example
`new MyService(new DatabaseConnection)`. In the tests you can then pass a mock
`new MyService(m::mock(DatabaseConnection::class))`.

That would mean that everywhere in your application where you create an object of this class you have to know where your
`DatabaseConnection` object is stored or create a new one. It's even more worse: when the interface for the class 
changes (for example an additional dependency get added) you will have to change this everywhere in your code.

Here comes the dependency injection into play. Here with the most strait forward and understandable way (a callback):

```php
$container->share('databaseConnection', function () {
    return new DatabaseConnection();
});
$container->add('myService', function () use ($container) {
    return new MyService($container->get('databaseConnection'));
});
```

### Make the container available

The container could also be available from within the class<sup>[*1]</sup>. This library provides a class with only
static methods `DI` to make the dependencies available from everywhere. It is using the same interface but with static
calls. The above example could look like this:

```php
DI::share('databaseConnection', function () {
    return new DatabaseConnection();
});
DI::add('myService', function () {
  return new MyService(); // we can access DI::get('databaseConnection') within this class now
});
```

- **&ast;<sup>1</sup>** Some people say this is hiding the dependencies and is an anti pattern called `Service Locator`. Don't trust them. It's still clear what are the dependencies (you just have to search for them) and it could be easier
  to write. But the most crucial change is that the instance gets created without the need of this instance. Assume you
  may need a `DatabaseConnection` only if the cache does not already store the result - such things can have a huge
  impact when we are talking about large amounts of users.
  
### Tests

Now when we want to test the class we can just replace the dependency for database connection:

```php
DI::share('databaseConnection', function () {
    return m::mock(DatabaseConnection::class);
});

DI::get('databaseConnection')->shouldReceive('query'); 
// ...
```

## Examples

For examples with tests have a look at the source: tests/examples.

Here are some small examples for basic usage.

### The `Config`
```php
<?php

use DependencyInjector\DI;

class Config {
    private static $_instance;
    
    public $database = [
        'host' => 'localhost',
        'user' => 'john',
        'password' => 'does.secret',
        'database' => 'john_doe'
    ];
    
    public $redis = ['host' => 'localhost'];
    
    private function __construct() {
        // maybe some logic to change the config or initialize variables
    }
    
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new Config();
        }
        return self::$_instance;
    }
}

DI::set('config', function() { return Config::getInstance(); });

function someStaticFunction() {
    // before
    if (empty(Config::getInstance()->database['host'])) {
        throw new Exception('No database host configured');
    }
    
    // now
    if (empty(DI::config()->database['host'])) {
        throw new Exception('No database host configured');
    }
    
    // or if you prefer
    if (empty(DI::get('config')->database['host'])) {
        throw new Exception('No database host configured');
    }
}
```

### The database connection
```php
<?php

use DependencyInjector\DI;

DI::set('database', function() {
    $dbConfig = DI::config()->database;
    
    $mysql = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
    
    if (!empty($mysql->connect_error)) {
        throw new Exception('could not connect to database (' . $mysql->connect_error . ')');
    }
    
    return $mysql;
});

function someStaticFunction() {
    // before it maybe looked like this
    $mysql = MyApp::getDatabaseConnection();
    
    // now
    $mysql = DI::database();
    
    $mysql->query('SELECT * FROM table');
}
```

The problem before: you can not mock the static function `MyApp::getDatabaseConnection()`. You also can not mock the 
static function `DI::database()` or `DI::get('database')`. But you can set the dependency to return a mock object:

```php
<?php

use DependencyInjector\DI;

class ApplicationTest extends PHPUnit_Framework_TestCase {
    public function testSomeStaticFunction() {
        $mock = $this->getMock(mysqli::class);
        $mock->expects($this->once())->method('query')
            ->with('SELECT * FROM table');
        DI::set('database', $mock);
            
        someStaticFunction();
    }
}
```
