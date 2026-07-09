## Dependency Injector — Usage Guide

This is a PSR-11-compatible dependency injection container for PHP.

### Two entry points

**`DI` (static, global access)** — use when dependencies need to be reachable from anywhere:
```php
use DependencyInjector\DI;

DI::share('db', fn() => new PDO($dsn, $user, $pass));
$db = DI::get('db');
```

**`Container` (instance)** — use when you want an explicit, injectable container:
```php
use DependencyInjector\Container;

$container = new Container();
$container->share('db', fn() => new PDO($dsn, $user, $pass));
DI::setContainer($container); // optional: wire it into DI static facade
```

### Registering dependencies

| Method | What it does |
|---|---|
| `DI::share($name, $getter)` | Registers a shared (singleton) dependency — built once, cached |
| `DI::add($name, $getter)` | Registers a non-shared dependency — new instance on every `get()` |
| `DI::instance($name, $obj)` | Stores an already-created object directly |
| `DI::alias($origin, $alias)` | Adds an additional name for an existing dependency |
| `DI::delete($name)` | Removes a dependency (useful in tests) |
| `DI::reset()` | Clears all dependencies and the container |

`$getter` can be: a **closure**, a **class name** (auto-selects factory type), or a **factory instance**.

### Factory types (auto-selected by `add`/`share`)

- **`CallableFactory`** — when `$getter` is a closure or callable
- **`ClassFactory`** — when `$getter` is a plain class name; supports constructor args and method calls:
  ```php
  DI::share('session', Session::class)
      ->addArguments('app-name', 3600)       // static values resolved as dependencies if defined, else used as-is
      ->addMethodCall('start');

  // Use StringArgument to bypass dependency resolution:
  use DependencyInjector\Factory\Argument\StringArgument;
  DI::add('view', View::class)->addArguments(new StringArgument('layout'));
  ```
- **`SingletonFactory`** — when the class has `::getInstance()`; wraps that call
- **`NamespaceFactory`** — pattern factory for an entire namespace:
  ```php
  use DependencyInjector\Factory\NamespaceFactory;
  DI::add(Controller::class, (new NamespaceFactory(DI::getContainer(), Controller::class)));
  DI::get(Controller\UserController::class, $request); // passes $request as extra arg
  ```

### Retrieving dependencies

```php
DI::get('name')           // resolves the dependency
DI::get('name', $arg)     // passes extra args (non-shared ClassFactory/NamespaceFactory)
DI::has('name')           // returns bool, does not build
DI::make(Foo::class, $a)  // instantiates directly, respects instance() overrides (useful in tests)
DI::get('config')         // magic shorthand: DI::config() also works via __callStatic
```

### Testing

Replace any dependency before the test runs:
```php
DI::instance('db', $this->createMock(PDO::class));
// or override the factory:
DI::share('db', fn() => m::mock(PDO::class));

// Clean up after each test:
DI::reset();
```

`DI::make(Foo::class)` respects `DI::instance(Foo::class, $mock)` — prefer `make()` over `new` in production code.

### Custom factories

Extend `AbstractFactory` (implements `SharableFactoryInterface`):
```php
class DbFactory extends \DependencyInjector\Factory\AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $cfg = $this->container->get('config')->database;
        return new PDO($cfg->dsn, $cfg->user, $cfg->pass);
    }
}

DI::add('db', new DbFactory(DI::getContainer()));
```

### IDE support via extension

```php
/** @method static Config config()
 *  @method static PDO    database() */
class DI extends \DependencyInjector\DI {}

/** @property Config config
 *  @method   Config config() */
class Container extends \DependencyInjector\Container {}
```

### Namespace auto-discovery

Register a namespace so the container finds `MyFactories\FooFactory` automatically when `foo` is requested:
```php
DI::registerNamespace('MyFactories', 'Factory'); // looks for MyFactories\<Ucfirst(name)>Factory
```

---

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
