# Changelog

All notable changes to Stub Generator will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned Features

- [ ] Template caching for improved performance
- [ ] Conditional sections (if/else logic)
- [ ] Loop/iteration support
- [ ] Custom delimiters
- [ ] Template validation
- [ ] Stub discovery/listing
- [ ] Hot reloading in development

---

## [1.0.0] - 2026-02-12

### 🎉 Initial Release

Stub Generator v1.0.0 is here! A powerful, fluent stub template processor for PHP.

### Added

#### Core Features
- **Fluent Interface** - Chainable methods for elegant code
- **Placeholder Replacement** - Support for `$PLACEHOLDER$` and `{{PLACEHOLDER}}` formats
- **Optional Sections** - Remove sections based on conditions using `# SECTION:name` markers
- **File Operations** - Read templates and save processed content
- **Custom Base Path** - Support for custom stub directories
- **Magic Methods** - Use stub object directly as string with `__toString()`

#### API Methods
- `StubGenerator::create()` - Static factory method
- `replace()` - Set/merge placeholder replacements
- `removeSection()` - Remove optional sections
- `render()` - Process and return content
- `getContents()` - Alias for render()
- `saveTo()` - Save processed content to file
- `setBasePath()` - Set custom stub directory
- `getBasePath()` - Get current base path
- `__toString()` - Magic method for string conversion

#### Exception Classes
- `StubNotFoundException` - Thrown when stub file not found
- `StubRenderException` - Thrown when rendering fails

#### Developer Experience
- **Automatic Uppercase** - Keys automatically converted to UPPERCASE
- **Backward Compatible** - Supports both `$KEY$` and `{{KEY}}` formats
- **Clear Exceptions** - Helpful error messages with context
- **Well Documented** - Comprehensive docblocks throughout
- **Type Safe** - Full PHP 8.4 type declarations

#### Quality & Testing
- **PHPUnit Tests** - Comprehensive test suite
- **PHPStan** - Static analysis at level 5
- **Laravel Pint** - Automatic code formatting
- **Rector** - Automated refactoring for PHP 8.4
- **Zero Dependencies** - Pure PHP, no external dependencies

### Technical Details

#### Requirements
- PHP 8.4 or higher

#### Features
- Fluent, chainable API
- Multiple placeholder formats
- Optional section removal
- File system operations
- Custom base path support
- Magic method support

#### Performance
- Lightweight and fast
- No external dependencies
- Efficient string replacement
- Minimal memory footprint

### Breaking Changes

This is the initial release, so there are no breaking changes.

### Known Issues

None at this time. Please report issues at https://github.com/pixielity-co/stub-generator/issues

### Credits

Inspired by:
- [Laravel Modules](https://github.com/nWidart/laravel-modules) - Original Stub class design
- [Laravel](https://laravel.com/) - Fluent API patterns

---

[Unreleased]: https://github.com/pixielity-co/stub-generator/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/pixielity-co/stub-generator/releases/tag/v1.0.0
