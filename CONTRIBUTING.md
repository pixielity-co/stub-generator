# Contributing to Stub Generator

Thank you for considering contributing to Stub Generator! This document outlines the process and guidelines for contributing.

## Code of Conduct

Be respectful, inclusive, and professional in all interactions.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/stub-generator.git`
3. Create a feature branch: `git checkout -b feature/your-feature-name`
4. Install dependencies: `composer install`

## Development Workflow

### Before Making Changes

1. Ensure all tests pass: `composer test`
2. Check code quality: `composer check`

### Making Changes

1. Write clean, well-documented code
2. Follow PSR-12 coding standards
3. Add comprehensive docblocks to all classes and methods
4. Write unit tests for new functionality
5. Update documentation as needed

### Code Style

We use Laravel Pint for code formatting:

```bash
# Check code style
composer lint

# Fix code style automatically
composer format
```

### Static Analysis

We use PHPStan at level 5 for static analysis:

```bash
# Run static analysis
composer typecheck
```

### Refactoring

We use Rector for automated refactoring:

```bash
# Preview refactoring changes
composer refactor:dry

# Apply refactoring
composer refactor
```

### Testing

Write comprehensive tests for all new features:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage
```

Test requirements:
- Unit tests for all new classes and methods
- Integration tests for complex workflows
- Minimum 80% code coverage for new code
- All tests must pass before submitting PR

### Quality Checks

Run all quality checks before committing:

```bash
# Run all checks (test, lint, typecheck, refactor)
composer check

# Fix all auto-fixable issues
composer fix
```

## Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples

```
feat(core): add template caching support

Add caching mechanism to improve performance when processing
the same stub multiple times.

Closes #123
```

```
fix(render): handle missing placeholders gracefully

Check if placeholder exists before replacement.
Display helpful error message when placeholder is missing.
```

## Pull Request Process

1. Update documentation for any changed functionality
2. Add tests for new features
3. Ensure all quality checks pass: `composer check`
4. Update CHANGELOG.md with your changes
5. Submit PR with clear description of changes
6. Link related issues in PR description
7. Wait for code review and address feedback

### PR Title Format

Use the same format as commit messages:

```
feat(core): add template caching support
```

### PR Description Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Unit tests added/updated
- [ ] Integration tests added/updated
- [ ] All tests passing
- [ ] Manual testing completed

## Checklist
- [ ] Code follows PSR-12 standards
- [ ] Docblocks added/updated
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] All quality checks pass
```

## Project Structure

```
stub-generator/
├── src/
│   ├── StubGenerator.php          # Main class
│   ├── Exceptions/
│   │   ├── StubNotFoundException.php
│   │   └── StubRenderException.php
│   └── Facades/
│       └── Stub.php                # Laravel facade
├── tests/
│   ├── Unit/                       # Unit tests
│   ├── Integration/                # Integration tests
│   └── fixtures/                   # Test fixtures
│       └── stubs/                  # Test stub files
├── build/                          # Build artifacts (gitignored)
└── vendor/                         # Dependencies (gitignored)
```

## Adding New Features

1. Create feature branch from `main`
2. Implement feature with tests
3. Add comprehensive docblocks
4. Update README.md with usage examples
5. Update CHANGELOG.md
6. Submit PR

### Feature Template

```php
<?php

declare(strict_types=1);

namespace Pixielity\StubGenerator;

/**
 * Brief description of what the feature does.
 *
 * Detailed explanation of the feature's purpose and behavior.
 *
 * @package Pixielity\StubGenerator
 */
class YourFeature
{
    /**
     * Method description.
     *
     * Detailed explanation of what the method does.
     *
     * @param  string $param Parameter description
     * @return mixed  Return value description
     */
    public function yourMethod(string $param): mixed
    {
        // Implementation here
    }
}
```

## Documentation

- Update README.md for user-facing changes
- Update inline code documentation (docblocks)
- Add examples for new features
- Update CHANGELOG.md

## Questions?

- Open an issue for bugs or feature requests
- Start a discussion for questions or ideas
- Check existing issues before creating new ones

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
