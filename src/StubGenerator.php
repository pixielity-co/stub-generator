<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator;

use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function mkdir;

use Pixielity\StubGenerator\Exceptions\StubNotFoundException;
use Pixielity\StubGenerator\Exceptions\StubRenderException;

use function preg_replace;
use function str_replace;

use Stringable;

use function strtoupper;

use Throwable;

/**
 * Stub Generator - Fluent Template Processor.
 *
 * A powerful, fluent stub template processor with support for placeholder
 * replacement and optional sections. Inspired by Laravel Modules' Stub class.
 *
 * Features:
 * - Fluent, chainable API for elegant code
 * - Placeholder replacement with automatic uppercase conversion
 * - Support for both $PLACEHOLDER$ and {{PLACEHOLDER}} formats
 * - Optional section removal using # SECTION:name markers
 * - File operations (read templates, save processed content)
 * - Custom base path support for flexible stub directories
 * - Magic __toString() method for direct string conversion
 *
 * Basic Usage:
 * ```php
 * // Simple rendering
 * $content = StubGenerator::create('template.stub', [
 *     'name' => 'John Doe',
 *     'email' => 'john@example.com',
 * ])->render();
 *
 * // Save to file
 * StubGenerator::create('docker/redis.yml', [
 *     'container_prefix' => 'myapp',
 *     'redis_port' => '6379',
 * ])->saveTo('/path/to/output', 'docker-compose.yml');
 *
 * // Remove optional sections
 * $content = StubGenerator::create('docker/elasticsearch.yml', [
 *     'elasticsearch_password' => 'secret',
 * ])
 * ->removeSection('kibana')
 * ->render();
 * ```
 *
 *
 * @author  Pixielity Team <team@pixielity.co>
 *
 * @version 1.0.0
 *
 * @see     https://github.com/pixielity-co/stub-generator
 */
class StubGenerator implements Stringable
{
    /**
     * Custom base path for stub files.
     *
     * When set, all stub files are loaded from this directory.
     * When null, uses the default path (package root /stubs).
     *
     * Set via: StubGenerator::setBasePath('/custom/path')
     */
    protected static ?string $basePath = null;

    /**
     * Create a new StubGenerator instance.
     *
     * Private constructor - use the static create() factory method instead.
     * This enforces the fluent interface pattern.
     *
     * @param string $path     Relative path to stub file
     * @param array  $replaces Placeholder replacements
     */
    private function __construct(
        protected string $path,
        /**
         * Placeholder replacements.
         *
         * Array of key-value pairs where keys are automatically converted to
         * uppercase and wrapped with delimiters ($KEY$ or {{KEY}}).
         *
         * Example:
         * ```php
         * [
         *     'name' => 'John',        // Becomes $NAME$ or {{NAME}}
         *     'email' => 'john@...',   // Becomes $EMAIL$ or {{EMAIL}}
         * ]
         * ```
         */
        protected array $replaces = []
    ) {}

    /**
     * Magic method to convert stub to string.
     *
     * Allows using the stub object directly as a string.
     * Automatically calls render() when the object is used in a string context.
     *
     * Example:
     * ```php
     * $stub = StubGenerator::create('template.stub', [...]);
     * echo $stub;  // Automatically calls render()
     * ```
     *
     * @return string Processed stub content
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        } catch (Throwable $throwable) {
            // __toString() cannot throw exceptions, so we return error message
            return 'Error rendering stub: ' . $throwable->getMessage();
        }
    }

    /**
     * Create a new stub generator instance (Factory Method).
     *
     * This is the primary way to create a StubGenerator instance.
     * Provides a fluent interface for chaining methods.
     *
     * Example:
     * ```php
     * $stub = StubGenerator::create('template.stub', [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     * ]);
     * ```
     *
     * @param  string $path     Relative path to stub file (e.g., 'docker/redis.yml')
     * @param  array  $replaces Placeholder replacements (keys auto-converted to uppercase)
     * @return self   New StubGenerator instance for method chaining
     */
    public static function create(string $path, array $replaces = []): self
    {
        return new self($path, $replaces);
    }

    /**
     * Set custom base path for stub files.
     *
     * When set, all stub files are loaded from this directory instead
     * of the default location. Useful for custom stub directories.
     *
     * Example:
     * ```php
     * // Set custom path
     * StubGenerator::setBasePath('/custom/stubs');
     *
     * // Now loads from /custom/stubs/template.stub
     * $stub = StubGenerator::create('template.stub');
     *
     * // Reset to default
     * StubGenerator::setBasePath(null);
     * ```
     *
     * @param string|null $path Custom base path, or null to use default
     */
    public static function setBasePath(?string $path): void
    {
        self::$basePath = $path;
    }

    /**
     * Get the current base path for stub files.
     *
     * Returns the custom base path if set, otherwise null.
     *
     * @return string|null Current base path, or null if using default
     */
    public static function getBasePath(): ?string
    {
        return self::$basePath;
    }

    /**
     * Set or merge placeholder replacements.
     *
     * Keys are automatically converted to uppercase and wrapped with delimiters.
     * Multiple calls to replace() will merge the replacements.
     *
     * Supported formats:
     * - Primary: $PLACEHOLDER$
     * - Legacy: {{PLACEHOLDER}}
     *
     * Example:
     * ```php
     * $stub->replace(['name' => 'John'])
     *      ->replace(['email' => 'john@example.com']);
     * ```
     *
     * @param  array $replaces Placeholder replacements to add/merge
     * @return self  Returns $this for method chaining
     */
    public function replace(array $replaces): self
    {
        // Merge new replacements with existing ones
        $this->replaces = array_merge($this->replaces, $replaces);

        return $this;
    }

    /**
     * Remove an optional section from the template.
     *
     * Sections are marked in templates using comments:
     * ```
     * # SECTION:name
     * ... content to remove ...
     * # END_SECTION:name
     * ```
     *
     * Example template:
     * ```yaml
     * services:
     *   elasticsearch:
     *     image: elasticsearch:8.11.0
     *
     *   # SECTION:kibana
     *   kibana:
     *     image: kibana:8.11.0
     *   # END_SECTION:kibana
     * ```
     *
     * Usage:
     * ```php
     * $stub->removeSection('kibana');  // Removes Kibana section
     * ```
     *
     * @param  string $name Section name to remove
     * @return self   Returns $this for method chaining
     */
    public function removeSection(string $name): self
    {
        // Build regex pattern to match section markers and content
        // Pattern: # SECTION:name ... # END_SECTION:name
        // The 's' modifier makes . match newlines
        $pattern = '/# SECTION:' . preg_quote($name, '/') . '.*?# END_SECTION:' . preg_quote($name, '/') . '/s';

        // Store the pattern for later use in render()
        // We don't modify content here to keep the method pure
        $this->replaces['__REMOVE_SECTION_' . $name . '__'] = $pattern;

        return $this;
    }

    /**
     * Render the stub with all replacements applied.
     *
     * This method:
     * 1. Reads the stub file from disk
     * 2. Removes any optional sections marked for removal
     * 3. Replaces all placeholders with their values
     * 4. Returns the processed content
     *
     * Placeholder formats supported:
     * - $PLACEHOLDER$ (primary, recommended)
     * - {{PLACEHOLDER}} (legacy, backward compatibility)
     *
     * Example:
     * ```php
     * $content = $stub->render();
     * ```
     *
     * @return string Processed stub content
     *
     * @throws StubNotFoundException If stub file not found
     * @throws StubRenderException   If rendering fails
     */
    public function render(): string
    {
        try {
            // Step 1: Read the stub file content
            $content = $this->getStubContent();

            // Step 2: Remove optional sections
            $content = $this->removeSections($content);

            // Step 3: Replace all placeholders
            $content = $this->replacePlaceholders($content);

            return $content;
        } catch (StubNotFoundException $e) {
            // Re-throw stub not found exceptions
            throw $e;
        } catch (Throwable $e) {
            // Wrap any other exceptions in StubRenderException
            throw new StubRenderException(
                'Failed to render stub "' . $this->path . '": ' . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Alias for render() method.
     *
     * Provides compatibility with Laravel Modules' Stub class API.
     *
     * @return string Processed stub content
     *
     * @throws StubNotFoundException If stub file not found
     * @throws StubRenderException   If rendering fails
     */
    public function getContents(): string
    {
        return $this->render();
    }

    /**
     * Render and save the processed content to a file.
     *
     * This method:
     * 1. Renders the stub content
     * 2. Creates the target directory if it doesn't exist
     * 3. Writes the content to the specified file
     *
     * Example:
     * ```php
     * $success = $stub->saveTo('/path/to/output', 'config.yml');
     * if ($success) {
     *     echo "File saved successfully!";
     * }
     * ```
     *
     * @param  string $path     Directory path where file should be saved
     * @param  string $filename Name of the file to create
     * @return bool   True on success, false on failure
     *
     * @throws StubNotFoundException If stub file not found
     * @throws StubRenderException   If rendering fails
     */
    public function saveTo(string $path, string $filename): bool
    {
        // Render the stub content
        $content = $this->render();

        // Ensure the target directory exists
        if (! is_dir($path)) {
            // Create directory with 0755 permissions (rwxr-xr-x)
            // Recursive = true to create parent directories
            mkdir($path, 0755, true);
        }

        // Combine path and filename
        $fullPath = rtrim($path, '/') . '/' . $filename;

        // Write content to file
        // Returns number of bytes written, or false on failure
        $result = file_put_contents($fullPath, $content);

        // Return true if write was successful (bytes written > 0)
        return $result !== false;
    }

    /**
     * Get the full path to the stub file.
     *
     * Resolves the full path by combining the base path with the relative path.
     * Uses custom base path if set, otherwise uses default location.
     *
     * Default location: package_root/stubs/
     *
     * @return string Full path to stub file
     *
     * @throws StubNotFoundException If stub file not found
     */
    protected function getPath(): string
    {
        // Determine base path
        if (self::$basePath !== null) {
            // Use custom base path
            $basePath = self::$basePath;
        } else {
            // Use default: package root /stubs directory
            // __DIR__ is src/, so go up one level to package root
            $basePath = dirname(__DIR__) . '/stubs';
        }

        // Combine base path with relative path
        $fullPath = rtrim($basePath, '/') . '/' . ltrim($this->path, '/');

        // Check if file exists
        if (! file_exists($fullPath)) {
            throw new StubNotFoundException($fullPath);
        }

        return $fullPath;
    }

    /**
     * Read the stub file content from disk.
     *
     * @return string Raw stub file content
     *
     * @throws StubNotFoundException If stub file not found
     */
    protected function getStubContent(): string
    {
        // Get full path to stub file (throws if not found)
        $path = $this->getPath();

        // Read file content
        $content = file_get_contents($path);

        // file_get_contents returns false on failure
        if ($content === false) {
            throw new StubNotFoundException($path);
        }

        return $content;
    }

    /**
     * Remove optional sections from content.
     *
     * Processes all section removal patterns stored in replaces array.
     * Section patterns are stored with __REMOVE_SECTION_ prefix.
     *
     * @param  string $content Content to process
     * @return string Content with sections removed
     */
    protected function removeSections(string $content): string
    {
        // Iterate through all replacements
        foreach ($this->replaces as $key => $value) {
            // Ensure key is string for str_starts_with
            if (! is_string($key)) {
                continue;
            }

            // Check if this is a section removal pattern
            if (str_starts_with($key, '__REMOVE_SECTION_')) {
                // $value contains the regex pattern
                // Remove the section using regex
                $content = preg_replace($value, '', $content);
            }
        }

        return $content;
    }

    /**
     * Replace all placeholders in content.
     *
     * Supports two placeholder formats:
     * - Primary: $PLACEHOLDER$
     * - Legacy: {{PLACEHOLDER}}
     *
     * Keys are automatically converted to uppercase.
     *
     * @param  string $content Content to process
     * @return string Content with placeholders replaced
     */
    protected function replacePlaceholders(string $content): string
    {
        // Build search and replace arrays
        $search = [];
        $replace = [];

        foreach ($this->replaces as $key => $value) {
            // Ensure key is string
            if (! is_string($key)) {
                continue;
            }

            // Skip section removal patterns
            if (str_starts_with($key, '__REMOVE_SECTION_')) {
                continue;
            }

            // Convert key to uppercase
            $upperKey = strtoupper($key);

            // Add both placeholder formats
            // Primary format: $KEY$
            $search[] = '$' . $upperKey . '$';
            $replace[] = $value;

            // Legacy format: {{KEY}}
            $search[] = '{{' . $upperKey . '}}';
            $replace[] = $value;
        }

        // Perform all replacements at once (more efficient than loop)
        return str_replace($search, $replace, $content);
    }
}
