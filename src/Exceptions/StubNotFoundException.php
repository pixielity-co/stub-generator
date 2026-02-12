<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a stub file cannot be found.
 *
 * This exception is thrown when:
 * - The specified stub file does not exist
 * - The stub file path is invalid
 * - The stub file is not readable
 *
 * Example:
 * ```php
 * try {
 *     $stub = StubGenerator::create('nonexistent.stub');
 *     $content = $stub->render();
 * } catch (StubNotFoundException $e) {
 *     echo "Stub not found: " . $e->getMessage();
 * }
 * ```
 *
 *
 * @author  Pixielity Team <team@pixielity.co>
 *
 * @version 1.0.0
 */
class StubNotFoundException extends RuntimeException
{
    /**
     * Create a new StubNotFoundException instance.
     *
     * Automatically formats the error message to include the file path.
     *
     * @param string $path Full path to the stub file that was not found
     */
    public function __construct(string $path)
    {
        parent::__construct("Stub file not found: {$path}");
    }
}
