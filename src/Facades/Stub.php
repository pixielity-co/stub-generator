<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator\Facades;

use Pixielity\StubGenerator\StubGenerator;
use Stringable;

/**
 * Stub Facade - Laravel-Style Static Interface.
 *
 * Provides a Laravel-style facade for the StubGenerator class.
 * Allows calling all StubGenerator methods statically through this facade.
 *
 * This facade uses __callStatic to delegate all static method calls to
 * a new StubGenerator instance, providing a convenient API similar to
 * Laravel's facades.
 *
 * Basic Usage:
 * ```php
 * use Pixielity\StubGenerator\Facades\Stub;
 *
 * // Create and render
 * $content = Stub::create('template.stub', [
 *     'name' => 'John Doe',
 * ])->render();
 *
 * // Save to file
 * Stub::create('docker/redis.yml', [
 *     'container_prefix' => 'myapp',
 * ])->saveTo('/path/to/output', 'docker-compose.yml');
 *
 * // Set custom base path
 * Stub::setBasePath('/custom/stubs');
 * ```
 *
 * Available Methods (delegated to StubGenerator):
 * - create(string $path, array $replaces = []): StubGenerator
 * - setBasePath(?string $path): void
 * - getBasePath(): ?string
 *
 * Instance Methods (after create()):
 * - replace(array $replaces): StubGenerator
 * - removeSection(string $name): StubGenerator
 * - render(): string
 * - getContents(): string
 * - saveTo(string $path, string $filename): bool
 *
 *
 * @author  Pixielity Team <team@pixielity.co>
 *
 * @version 1.0.0
 *
 * @method static StubGenerator create(string $path, array $replaces = []) Create a new stub generator instance
 * @method static void          setBasePath(?string $path)                 Set custom base path for stub files
 * @method static string|null   getBasePath()                              Get the current base path for stub files
 *
 * @see     StubGenerator
 */
class Stub implements Stringable
{
    /**
     * Create a new Stub facade instance.
     *
     * Allows using the facade as an object:
     * ```php
     * $stub = new Stub();
     * $content = $stub->create('template.stub')->render();
     * ```
     *
     * @param StubGenerator|null $instance Optional StubGenerator instance
     */
    public function __construct(protected ?StubGenerator $instance = null) {}

    /**
     * Handle static method calls.
     *
     * Delegates all static method calls to the StubGenerator class.
     * This is the core of the facade pattern.
     *
     * Example:
     * ```php
     * // Calls StubGenerator::create()
     * $stub = Stub::create('template.stub');
     *
     * // Calls StubGenerator::setBasePath()
     * Stub::setBasePath('/custom/path');
     * ```
     *
     * @param  string $method    Method name to call
     * @param  array  $arguments Method arguments
     * @return mixed  Result from the delegated method call
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        // Delegate to StubGenerator class
        // This allows: Stub::create() -> StubGenerator::create()
        // @phpstan-ignore staticMethod.dynamicName
        return StubGenerator::$method(...$arguments);
    }

    /**
     * Handle instance method calls.
     *
     * Delegates all instance method calls to the underlying StubGenerator instance.
     * Allows using the facade as an object.
     *
     * Example:
     * ```php
     * $stub = new Stub(StubGenerator::create('template.stub'));
     * $content = $stub->render();  // Calls $instance->render()
     * ```
     *
     * @param  string $method    Method name to call
     * @param  array  $arguments Method arguments
     * @return mixed  Result from the delegated method call
     */
    public function __call(string $method, array $arguments): mixed
    {
        // If no instance exists, create one using the first argument as path
        if (! $this->instance instanceof StubGenerator) {
            // Assume first call is create() with path and optional replaces
            $path = $arguments[0] ?? '';
            $replaces = $arguments[1] ?? [];
            $this->instance = StubGenerator::create($path, $replaces);

            return $this;
        }

        // Delegate to the StubGenerator instance
        // @phpstan-ignore method.dynamicName
        $result = $this->instance->$method(...$arguments);

        // If result is the instance (for chaining), return $this
        if ($result === $this->instance) {
            return $this;
        }

        return $result;
    }

    /**
     * Magic method to convert facade to string.
     *
     * Delegates to the underlying StubGenerator instance's __toString() method.
     *
     * Example:
     * ```php
     * $stub = new Stub(StubGenerator::create('template.stub', [...]));
     * echo $stub;  // Automatically renders the stub
     * ```
     *
     * @return string Processed stub content
     */
    public function __toString(): string
    {
        if (! $this->instance instanceof StubGenerator) {
            return '';
        }

        return (string) $this->instance;
    }

    /**
     * Get the underlying StubGenerator instance.
     *
     * Useful when you need direct access to the StubGenerator instance.
     *
     * Example:
     * ```php
     * $stub = new Stub();
     * $generator = $stub->getInstance();
     * ```
     *
     * @return StubGenerator|null The underlying instance, or null if not set
     */
    public function getInstance(): ?StubGenerator
    {
        return $this->instance;
    }

    /**
     * Set the underlying StubGenerator instance.
     *
     * Allows injecting a StubGenerator instance into the facade.
     *
     * Example:
     * ```php
     * $stub = new Stub();
     * $stub->setInstance(StubGenerator::create('template.stub'));
     * ```
     *
     * @param  StubGenerator $instance The StubGenerator instance to use
     * @return self          Returns $this for method chaining
     */
    public function setInstance(StubGenerator $instance): self
    {
        $this->instance = $instance;

        return $this;
    }
}
