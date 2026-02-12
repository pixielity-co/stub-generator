<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Pixielity\StubGenerator\Exceptions\StubNotFoundException;
use Pixielity\StubGenerator\StubGenerator;

/**
 * Unit tests for StubGenerator class.
 *
 * Tests all core functionality including:
 * - Factory method creation
 * - Placeholder replacement (both formats)
 * - Section removal
 * - File operations
 * - Path resolution
 * - Error handling
 */
class StubGeneratorTest extends TestCase
{
    /**
     * Tear down after each test.
     *
     * Reset the base path to null to avoid test interference.
     */
    protected function tearDown(): void
    {
        StubGenerator::setBasePath(null);
        parent::tearDown();
    }

    /**
     * Test that create() factory method returns StubGenerator instance.
     */
    public function test_it_creates_instance_with_factory_method(): void
    {
        $stub = StubGenerator::create('test.stub');

        $this->assertInstanceOf(StubGenerator::class, $stub);
    }

    /**
     * Test that placeholders are replaced with $KEY$ format.
     */
    public function test_it_replaces_placeholders_with_primary_format(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-simple.stub', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ])->render();

        $this->assertStringContainsString('Hello John Doe!', $content);
        $this->assertStringContainsString('Your email is: john@example.com', $content);
    }

    /**
     * Test that placeholders are replaced with {{KEY}} format.
     */
    public function test_it_replaces_placeholders_with_legacy_format(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-legacy.stub', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ])->render();

        $this->assertStringContainsString('Hello Jane Doe!', $content);
        $this->assertStringContainsString('Your email is: jane@example.com', $content);
    }

    /**
     * Test that keys are automatically converted to uppercase.
     */
    public function test_it_converts_keys_to_uppercase(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-simple.stub', [
            'name' => 'Test',  // lowercase key
            'EMAIL' => 'test@example.com',  // uppercase key
        ])->render();

        $this->assertStringContainsString('Hello Test!', $content);
        $this->assertStringContainsString('test@example.com', $content);
    }

    /**
     * Test that both placeholder formats work in the same file.
     */
    public function test_it_supports_mixed_placeholder_formats(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-mixed.stub', [
            'name' => 'Mixed',
            'email' => 'mixed@example.com',
            'value' => 'TestValue',
        ])->render();

        $this->assertStringContainsString('Primary: Mixed', $content);
        $this->assertStringContainsString('Legacy: mixed@example.com', $content);
        $this->assertStringContainsString('Both formats work: TestValue and TestValue', $content);
    }

    /**
     * Test that methods can be chained.
     */
    public function test_it_chains_methods(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $stub = StubGenerator::create('test-simple.stub')
            ->replace(['name' => 'Chain'])
            ->replace(['email' => 'chain@example.com']);

        $this->assertInstanceOf(StubGenerator::class, $stub);

        $content = $stub->render();
        $this->assertStringContainsString('Hello Chain!', $content);
    }

    /**
     * Test that replace() returns self for chaining.
     */
    public function test_it_returns_self_from_replace(): void
    {
        $stub = StubGenerator::create('test.stub');
        $result = $stub->replace(['key' => 'value']);

        $this->assertSame($stub, $result);
    }

    /**
     * Test that removeSection() returns self for chaining.
     */
    public function test_it_returns_self_from_remove_section(): void
    {
        $stub = StubGenerator::create('test.stub');
        $result = $stub->removeSection('optional');

        $this->assertSame($stub, $result);
    }

    /**
     * Test that optional sections can be removed.
     */
    public function test_it_removes_optional_sections(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-sections.stub', [
            'optional_value' => 'Optional',
            'another_value' => 'Another',
        ])
            ->removeSection('optional')
            ->render();

        $this->assertStringContainsString('Main content here.', $content);
        $this->assertStringNotContainsString('This is optional content', $content);
        $this->assertStringNotContainsString('Optional', $content);
        $this->assertStringContainsString('This is another optional section.', $content);
    }

    /**
     * Test that multiple sections can be removed.
     */
    public function test_it_removes_multiple_sections(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $content = StubGenerator::create('test-sections.stub')
            ->removeSection('optional')
            ->removeSection('another')
            ->render();

        $this->assertStringContainsString('Main content here.', $content);
        $this->assertStringNotContainsString('This is optional content', $content);
        $this->assertStringNotContainsString('This is another optional section.', $content);
        $this->assertStringContainsString('End of content.', $content);
    }

    /**
     * Test that saveTo() creates file successfully.
     */
    public function test_it_saves_to_file(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $outputPath = __DIR__ . '/../fixtures/output';
        $filename = 'test-output.txt';

        $success = StubGenerator::create('test-simple.stub', [
            'name' => 'Save Test',
            'email' => 'save@example.com',
        ])->saveTo($outputPath, $filename);

        $this->assertTrue($success);
        $this->assertFileExists($outputPath . '/' . $filename);

        // Cleanup
        unlink($outputPath . '/' . $filename);
        rmdir($outputPath);
    }

    /**
     * Test that saveTo() creates directory if needed.
     */
    public function test_it_creates_directory_if_needed(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $outputPath = __DIR__ . '/../fixtures/nested/deep/path';
        $filename = 'test.txt';

        $success = StubGenerator::create('test-simple.stub', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ])->saveTo($outputPath, $filename);

        $this->assertTrue($success);
        $this->assertDirectoryExists($outputPath);
        $this->assertFileExists($outputPath . '/' . $filename);

        // Cleanup
        unlink($outputPath . '/' . $filename);
        rmdir($outputPath);
        rmdir(__DIR__ . '/../fixtures/nested/deep');
        rmdir(__DIR__ . '/../fixtures/nested');
    }

    /**
     * Test that exception is thrown for missing stub file.
     */
    public function test_it_throws_exception_for_missing_stub(): void
    {
        $this->expectException(StubNotFoundException::class);
        $this->expectExceptionMessage('Stub file not found:');

        StubGenerator::create('nonexistent.stub')->render();
    }

    /**
     * Test that custom base path can be set.
     */
    public function test_it_uses_custom_base_path(): void
    {
        $customPath = __DIR__ . '/../fixtures/stubs';
        StubGenerator::setBasePath($customPath);

        $this->assertEquals($customPath, StubGenerator::getBasePath());

        $content = StubGenerator::create('test-simple.stub', [
            'name' => 'Custom Path',
            'email' => 'custom@example.com',
        ])->render();

        $this->assertStringContainsString('Hello Custom Path!', $content);
    }

    /**
     * Test that getContents() is an alias for render().
     */
    public function test_get_contents_is_alias_for_render(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $stub = StubGenerator::create('test-simple.stub', [
            'name' => 'Alias Test',
            'email' => 'alias@example.com',
        ]);

        $renderContent = $stub->render();
        $getContentsContent = $stub->getContents();

        $this->assertEquals($renderContent, $getContentsContent);
    }

    /**
     * Test that __toString() magic method works.
     */
    public function test_it_converts_to_string(): void
    {
        StubGenerator::setBasePath(__DIR__ . '/../fixtures/stubs');

        $stub = StubGenerator::create('test-simple.stub', [
            'name' => 'String Test',
            'email' => 'string@example.com',
        ]);

        $content = (string) $stub;

        $this->assertStringContainsString('Hello String Test!', $content);
        $this->assertStringContainsString('string@example.com', $content);
    }
}
