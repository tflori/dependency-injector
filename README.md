# Dependency Injector

A simple and lightweight dependency injector. You will need nothing more to tests your old legacy code.

Did you ever wondered how complicated it could be to write a dependency injector that works? In fact it is very easy
and you don't need a tons of classes laying around and learn a dozens of new fancy words. 

It is just a storage of functions and values for a specific key. Store your function to create an instance and it get
executed the first time you need this instance.

Sounds to easy? What about dependencies? Check the examples. It is nothing more needed than this.
 
## What is a dependency

Something that your script needs to work correct. For example an instance of `Calculator` or `Config`. Or even
a class itself.

## Tests

The problem is always to test things and to provide mocks for your tests. This DependencyInjector can solve this
problems. It is possible to call your `getInstance()` method four your singleton like this:
`(DI::get(MySingleton::class))::getInstance()` - now you can provide a mock object under this key.

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
