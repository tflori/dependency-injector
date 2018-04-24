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
<?php
$container->share('databaseConnection', function () {
    return new DatabaseConnection();
});
$container->add('myService', function () use ($container) {
    return new MyService($container->get('databaseConnection'));
});
```

### Make the container available

The container could also be available from within the class &ast;<sup>1</sup>. This library provides a class with only
static methods `DI` to make the dependencies available from everywhere. It is using the same interface but with static
calls. The above example could look like this:

```php
<?php
DI::share('databaseConnection', function () {
    return new DatabaseConnection();
});
DI::add('myService', function () {
  return new MyService(); // we can access DI::get('databaseConnection') within this class now
});
```

### Tests

Now when we want to test the class we can just replace the dependency for database connection:

```php
<?php
DI::share('databaseConnection', function () {
    return m::mock(DatabaseConnection::class);
});

DI::get('databaseConnection')->shouldReceive('query'); 
// ...
```

This works in both versions &ast;<sup>2</sup> and can safely be used for testing.

## Advanced usage

We can not only store callbacks that are executed when a new instance is required. There are some other practical ways
that makes it easier for you to define how dependencies should be resolved.

### Define instances

Instances can be defined to be returned when a dependency is requested. Keep in mind that you will have to instantiate
a class before using it what might have an impact in performance. Anyway this gives you an opportunity to also define
several values for example a very simple configuration:

```php
<?php
$container->instance('config', (object)[
    'database' => (object)[
        'dsn' => 'mysql://whatever',
        'user' => 'john',
        'password' => 'does_secret',
    ]
]);
```

### Define aliases

Aliases allow you to have several names for a dependency. First define the dependency and than alias it:

```php
<?php
$container->share(Config::class, Config::class);
$container->alias(Config::class, 'config');
$container->alias(Config::class, 'cfg'); 
```

### Factories

The resolving of a dependency is done by an instance of a `FactoryInterface`. You can write your own factories or use
the existing factory implementations:

- `CallableFactory` A factory that calls a callable to get the instance (or value)
- `ClassFactory` This factory creates a instance of the given class (with arguments and method calls)
- `SingletonFactory` Singleton classes can be passed through the container to provide mocks instead
- `Instance` This is a pseudo factory that just holds a instance (or value)
- `Alias` The second pseudo factory that just requests another dependency

#### Own factories

When you write own factories you will have to implement `FactoryInterface` or `SharableFactoryInterface`. The
`AbstractFactory` implements `SharableFactoryInterface` and can be extended to your needs in a very simple way:

```php
<?php
class DatabaseFactory extends \DependencyInjector\Factory\AbstractFactory
{
    protected $shared = true; // false is default - so simple omit it for non shared factories or use share to define
    
    protected function build()
    {
        $dbConfig = $this->container->get('config')->database;
        return new PDO($dbConfig->dsn, $dbConfig->user, $dbConfig->password);
    }
}
```

Own factories can be defined be used for resolving dependencies by `Container::add()` or `Container::share()`

## Examples

Here are some small examples for basic usage.

### The `Config`
```php
<?php
$container = new \DependencyInjector\Container();

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

$container->add('config', Config::class); // adds a SingletonFactory

DI::setContainer($container);

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

- **&ast;<sup>1</sup>** Some people say this is hiding the dependencies and is an anti pattern called `Service Locator`. Don't trust them. It's still clear what are the dependencies (you just have to search for them) and it could be easier
  to write. But the most crucial difference is that otherwise the instance gets created without a requirement. Assume
  you may need a `DatabaseConnection` only if the cache does not already store the result - such things can have a huge
  impact when we are talking about large amounts of users.
  
- **&ast;<sup>2</sup>** In [the meta document](https://www.php-fig.org/psr/psr-11/meta/) for PSR-11 they mention that
  it is harder to test when you pass the container to your objects. But - as we can see - it's not.
