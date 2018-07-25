# Dependency Injector

[![build status](https://gitlab.w00tserver.org/tflori/dependency-injector/badges/master/build.svg)](https://gitlab.w00tserver.org/tflori/dependency-injector/commits/master)
[![Coverage Status](https://coveralls.io/repos/github/tflori/dependency-injector/badge.svg?branch=master)](https://coveralls.io/github/tflori/dependency-injector?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/dependency-injector/v/stable)](https://packagist.org/packages/tflori/dependency-injector)
[![Total Downloads](https://poser.pugx.org/tflori/dependency-injector/downloads)](https://packagist.org/packages/tflori/dependency-injector)
[![License](https://poser.pugx.org/tflori/dependency-injector/license)](https://packagist.org/packages/tflori/dependency-injector)

A simple and lightweight dependency injector. Compatible to php standard recommendation for dependency injection 
containers ([PSR-11](https://www.php-fig.org/psr/psr-11/)).
 
## What Is A Dependency

Something that your application needs to work correct. For example an instance of `Calculator` or `Config` or an object
that implements `CacheInterface`.

## Basic Usage

One of the biggest problems in software development is the tight coupling of dependencies. Imagine a class that creates
an instance from `DatabaseConnection` with `new DatabaseConnection()`. This is called a tight coupling - it is so tight
that you would have to overwrite your autoloader (what is not always possible) to mock the database connection.

There are two solutions to test this class without the need to have a database connection during the tests.

### Passing The Dependency

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

### Make The Container Available

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

> We are using here the static methods from DI in the rest of the document.

## Advanced Usage

We can not only store callbacks that are executed when a new instance is required. There are some other practical ways
that makes it easier for you to define how dependencies should be resolved.

### Make An Object

With version 2.1 comes the new method `DI::make(string $class, ...$args)` which allows you to directly get an instance
of `$class` with `$args` as constructor arguments without defining a dependency for it.

```php
<?php
$feed = DI::make(SomeFeed::class, $_GET['id']);
// equals to  new SomeFeed($_GET['id']);
``` 

Even if the above examples are equal the method has a big advantage: you can provide a mock for the class.

```php
<?php
DI::instance(SomeFeed::class, m::mock(SomeFeed::class));
$feedMock = DI::make(SomeFeed::class, $_GET['id']);
```

### Define Instances

Instances can be defined to be returned when a dependency is requested. Keep in mind that you will have to instantiate
a class before using it what might have an impact in performance. Anyway this gives you an opportunity to also define
several values for example a very simple configuration:

```php
<?php
DI::instance('config', (object)[
    'database' => (object)[
        'dsn' => 'mysql://whatever',
        'user' => 'john',
        'password' => 'does_secret',
    ]
]);
```

> Do not misuse this as a global storage. You will get naming conflicts and we will not provide solutions for it.

### Define Aliases

Aliases allow you to have several names for a dependency. First define the dependency and than alias it:

```php
<?php
DI::share(Config::class, Config::class);
DI::alias(Config::class, 'config');
DI::alias(Config::class, 'cfg'); 
```

### Define Dependencies

Dependencies that are built when they are requested can be added using `Container::add(string $name, $getter)`. The
getter can be a callable (such as closures - what we did above), a class name of a factory, an instance of a factory or
any other class name.

> Factory here means a class that implements `FactoryInterface` or `SharableFactoryInterface`.

`Container:add()` will return the factory that got added and which factory get added is defined as:

- An instance of a factory: the given factory
- A class name of a factory: a new object of the given class
- A class name of a class using singleton pattern: a `SingletonFactory`
- Any other class name: a `ClassFactory`
- A callable: a `CallableFactory`

Dependencies using factories implementing `SharableFactoryInterface` can be shared by calling `$factory->share()` or
using the shortcut `Container::share(string $name, $getter)`. 
 
#### Class Factory

The `ClassFactory` creates only an instance without any arguments by default. It also allows to pass different arguments
to the constructor and that is the usual way suggested from PSR-11:

```php
<?php
// pass some statics
DI::share('session', Session::class)
    ->addArguments('app-name', 3600, true);
new Session(DI::has('app-name') ? DI::get('app-name') : 'app-name', 3600, true);

// pass dependencies
DI::share('database', Connection::class)
    ->addArguments('config');
new Connection(DI::has('config') ? DI::get('config') : 'config');

// pass a string that is defined as dependency
DI::add('view', View::class)
    ->addArguments(new StringArgument('default-layout'));
new View('default-layout');
```

It is also possible to call methods on the new instance:

```php
<?php
DI::share('cache', Redis::class)
    ->addMethodCall('connect', 'localhost', 4321, 1);

// you can also bypass resolving the dependency
DI::add('view', View::class)
    ->addMethodCall('setView', new StringArgument('default-view'));
```

Non shared classes allow to pass additional arguments to the constructor:

```php
<?php
DI::add('view', View::class);
$view = DI::get('view', 'login');
new View('login');
```

#### Singleton Factory

The `SingletonFactory` is a special factory that just wraps the call to `::getInstance()`. The advantage here is that
you don't have to create the instance if you don't need to or create a mock object for tests. Without this factory you
can either pass an instance of the class or stick with the call to `::getInstance()` in your code.

This factory also allows pass arguments to the `::getInstance()` method for classes that store different instances for
specific arguments.

```php
<?php
DI::add('calculator', Calculator::class);

DI::get('calculator', 'rad');
Calculator::getInstance('rad');

DI::get('calculator', 'deg');
Calculator::getInstance('deg');
```

#### Callable Factory

This factory is just calling the passed callback. The callback only have to be callable what is checked with 
`is_callable($getter)` - so you can also pass an array with class or instance and method name.

```php
<?php
DI::share('database', function() {
    $config = DI::get('config');
    return new PDO($config->database->dsn, $config->database->username, $config->database->password);
});
```

> Because the callback could also be a static method from a class with `[Calculator::class, 'getInstance']`. It is also
> possible to use this for Singleton classes. The difference is that this could be shared but the `SingletonFactory`
> always calls `::getInstance()` what is the preferred method from our point of view.
 
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

Factories can be defined for dependencies using `Container::add()` or `Container::share()` as described above. But you
can also register the namespace where your factories are defined and the container will try to find the factory for
the requested dependency. When you request a dependency and it is not already defined it will check each registered
namespace for a class named `$namespace . '\\' . ucfirst($dependency) . $suffix`.  

## Examples

Here are some small examples how you could use this library.

### The Configuration

```php
<?php
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

DI::add('config', Config::class); // adds a SingletonFactory

function someStaticFunction() {
    // before
    if (empty(Config::getInstance()->database['host'])) {
        throw new Exception('No database host configured');
    }
    
    // now
    if (empty(DI::get('config')->database['host'])) {
        throw new Exception('No database host configured');
    }
}
```

### The Database Connection

```php
<?php
DI::set('database', function() {
    $dbConfig = DI::get('config')->database;
    
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
    $mysql = DI::get('database');
    
    $mysql->query('SELECT * FROM table');
}
```

The problem before: you can not mock the static function `MyApp::getDatabaseConnection()`. You also can not mock the 
static function `DI::get('database')`. But you can set the dependency to return a mock object:

```php
<?php
class ApplicationTest extends TestCase {
    public function testSomeStaticFunction() {
        // prepare the mock
        $mock = $this->getMock(mysqli::class);
        $mock->expects($this->once())->method('query')
            ->with('SELECT * FROM table');
        
        // overwrite the dependency
        DI::instance('database', $mock);
            
        someStaticFunction();
    }
}
```

## Tips

### Extend The DI Class

When you are using the `DI` class it makes sense to extend this class and add annotations for the `__callStatic()` getter
so that your IDE knows what comes back from your `DI`:

```php
<?php
/**
 * @method static Config config()
 * @method static mysqli database() 
 */
class DI extends \DependencyInjector\DI {}
``` 

### Extend The Container Class

Similar functionality exists for `Container`. The magic method `__isset()` aliases `Container::has()`, `__get()` aliases
`Container::get($name)` and `__call()` aliases `Container::get($name, ...$args)`. So you can annotate your container
like this:

```php
<?php
/** 
 * @property Config config
 * @method Config config()
 */
class Container extends \DependencyInjector\Container {}
```

## Comments

- **&ast;<sup>1</sup>** Some people say this is hiding the dependencies and is an anti pattern called `Service Locator`.
  Don't trust them. It's still clear what are the dependencies (you just have to search for them) and it could be easier
  to write. But the most crucial difference is that otherwise the instance gets created without a requirement. Assume
  you may need a `DatabaseConnection` only if the cache does not already store the result - such things can have a huge
  impact when we are talking about large amounts of users.
  
- **&ast;<sup>2</sup>** In [the meta document](https://www.php-fig.org/psr/psr-11/meta/) for PSR-11 they mention that
  it is harder to test when you pass the container to your objects. But - as we can see - it's not.
