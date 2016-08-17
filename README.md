# Dependency Injector

A simple and lightweight dependency injector. You will need nothing more to tests your old legacy code.
 
## What is a dependency

Something that your script needs to work correct. For example an instance of `Calculator` or `Config`. Or even
a class itself.

## Examples

### The `Config`
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

class_alias('DependencyInjector', 'DI'); // create an alias for easier access

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

The problem before: you can not mock the call to `MyApp::getDatabaseConnection()`. You can still not mock the call to 
`DI::database()` or `DI::get('database')`. But you can set the dependency to return a mock object:

```php
<?php

class ApplicationTest extends PHPUnit_Framework_TestCase {
    public function testSomeStaticFunction() {
        $mock = $this->getMock(mysqli::class);
        $mock->expects($this->once())->method('query')
            ->with('SELECT * FROM table');
        DI::set('database', function() use($mock) { return $mock; });
            
        someStaticFunction();
    }
}
```
