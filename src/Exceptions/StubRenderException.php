<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception thrown when stub rendering fails.
 *
 * This exception is thrown when:
 * - File read operation fails
 * - Placeholder replacement fails
 * - Section removal fails
 * - Any other rendering error occurs
 *
 * Example:
 * ```php
 * try {
 *     $stub = StubGenerator::create('template.stub', [...]);
 *     $content = $stub->render();
 * } catch (StubRenderException $e) {
 *     echo "Rendering failed: " . $e->getMessage();
 *     // Access previous exception if available
 *     if ($e->getPrevious()) {
 *         echo "Caused by: " . $e->getPrevious()->getMessage();
 *     }
 * }
 * ```
 *
 *
 * @author  Pixielity Team <team@pixielity.co>
 *
 * @version 1.0.0
 */
class StubRenderException extends RuntimeException
{
    /**
     * Create a new StubRenderException instance.
     *
     * Wraps the original exception to provide context about the rendering failure.
     *
     * @param string         $message  Error message describing the failure
     * @param Throwable|null $previous Previous exception that caused this failure
     */
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct("Failed to render stub: {$message}", 0, $previous);
    }
}
