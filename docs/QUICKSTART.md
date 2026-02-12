# Quick Start Guide

Get started with Stub Generator in 5 minutes!

## Installation

```bash
composer require pixielity/stub-generator
```

## Basic Usage

### 1. Create a Stub Template

Create a file `stubs/welcome.stub`:

```
Hello $NAME$!

Welcome to $APP_NAME$.
Your email is: $EMAIL$
```

### 2. Process the Stub

```php
<?php

use Pixielity\StubGenerator\StubGenerator;

// Set the stubs directory
StubGenerator::setBasePath(__DIR__ . '/stubs');

// Create and render
$content = StubGenerator::create('welcome.stub', [
    'name' => 'John Doe',
    'app_name' => 'My App',
    'email' => 'john@example.com',
])->render();

echo $content;
```

Output:
```
Hello John Doe!

Welcome to My App.
Your email is: john@example.com
```

## Advanced Usage

### Save to File

```php
StubGenerator::create('config.stub', [
    'database' => 'mydb',
    'host' => 'localhost',
])->saveTo('/path/to/output', 'config.yml');
```

### Optional Sections

Template with optional section:
```yaml
# config.yml
database:
  host: $DB_HOST$
  name: $DB_NAME$

# SECTION:redis
redis:
  host: $REDIS_HOST$
  port: $REDIS_PORT$
# END_SECTION:redis
```

Remove section:
```php
// Without Redis
$content = StubGenerator::create('config.yml', [
    'db_host' => 'localhost',
    'db_name' => 'mydb',
])
->removeSection('redis')
->render();
```

### Using the Facade

```php
use Pixielity\StubGenerator\Facades\Stub;

// Same API, cleaner syntax
$content = Stub::create('template.stub', [
    'key' => 'value',
])->render();
```

## Next Steps

- Read the [full documentation](README.md)
- Check out [examples](examples/)
- Explore the [API reference](README.md#api-reference)

## Need Help?

- [GitHub Issues](https://github.com/pixielity-co/stub-generator/issues)
- [Documentation](README.md)
