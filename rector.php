<?php

/**
 * Rector Configuration - Stub Generator.
 *
 * Automated refactoring and code modernization for the Stub Generator package.
 *
 * @see https://github.com/rectorphp/rector
 * @see https://getrector.com/documentation
 *
 * Usage:
 *   Preview changes:  composer refactor:dry
 *   Apply changes:    composer refactor
 *
 * @version 1.0.0
 *
 * @author Pixielity Development Team
 */

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    // =========================================================================
    // PATHS CONFIGURATION
    // =========================================================================
    ->withPaths([
        __DIR__ . '/src',
    ])
    // =========================================================================
    // CACHE CONFIGURATION
    // =========================================================================
    ->withCache(__DIR__ . '/build/rector')
    // =========================================================================
    // SKIP CONFIGURATION
    // =========================================================================
    ->withSkip([
        // =====================================================================
        // PATHS TO SKIP
        // =====================================================================
        // Third-party dependencies
        '*/vendor/*',
        __DIR__ . '/vendor',
        // Build artifacts
        '*/build/*',
        '*/var/*',
        // Test fixtures
        '*/tests/fixtures/*',
        // =====================================================================
        // RULES TO SKIP
        // =====================================================================
        // Don't convert string interpolation to sprintf
        EncapsedStringsToSprintfRector::class,
        // Don't force newlines after statements (formatting handled by Pint)
        NewlineAfterStatementRector::class,
        // Don't add string casts when types are already known
        NullToStrictStringFuncCallArgRector::class,
    ])
    // =========================================================================
    // PHP VERSION TARGET
    // =========================================================================
    ->withPhpSets(
        php84: true  // Target PHP 8.4 features
    )
    // =========================================================================
    // RULE SETS
    // =========================================================================
    ->withSets([
        // Dead code removal
        SetList::DEAD_CODE,
        // Code quality improvements
        SetList::CODE_QUALITY,
        // Coding style consistency
        SetList::CODING_STYLE,
        // Early return pattern
        SetList::EARLY_RETURN,
        // Privatization
        SetList::PRIVATIZATION,
        // Type declarations
        SetList::TYPE_DECLARATION,
        // Naming conventions
        SetList::NAMING,
        // Instanceof checks
        SetList::INSTANCEOF,
    ])
    // =========================================================================
    // IMPORT NAMES CONFIGURATION
    // =========================================================================
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        importShortClasses: false,
        removeUnusedImports: true,
    )
    // =========================================================================
    // PARALLEL PROCESSING
    // =========================================================================
    ->withParallel(
        timeoutSeconds: 300,
        maxNumberOfProcess: 8,
        jobSize: 15,
    )
    // =========================================================================
    // ADDITIONAL CONFIGURATION
    // =========================================================================
    ->withFileExtensions(['php'])
    ->withRootFiles()
    ->withMemoryLimit('2G');
