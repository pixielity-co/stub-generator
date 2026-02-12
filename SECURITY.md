# Security Policy

## Supported Versions

We release patches for security vulnerabilities in the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

We take the security of Stub Generator seriously. If you discover a security vulnerability, please follow these steps:

### 1. Do Not Disclose Publicly

Please do not open a public issue or discuss the vulnerability in public forums, pull requests, or social media.

### 2. Report Privately

Send a detailed report to the maintainers via:

- **Email**: security@pixielity.co
- **GitHub Security Advisory**: Use the "Security" tab in the repository to create a private security advisory

### 3. Include Details

Your report should include:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact and severity
- Suggested fix (if you have one)
- Your contact information

### Example Report

```
Subject: [SECURITY] Path Traversal in Stub File Loading

Description:
The StubGenerator does not properly sanitize file paths before loading
stub files, allowing potential path traversal attacks.

Steps to Reproduce:
1. Call: StubGenerator::create('../../../etc/passwd')
2. The file is loaded from outside the intended stub directory

Impact:
Arbitrary file read with the privileges of the PHP process

Suggested Fix:
Validate and sanitize file paths using realpath() and ensure they
remain within the allowed stub directory.

Reporter: John Doe (john@example.com)
```

## Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Fix Timeline**: Depends on severity
  - Critical: Within 7 days
  - High: Within 14 days
  - Medium: Within 30 days
  - Low: Next regular release

## Security Update Process

1. **Acknowledgment**: We'll acknowledge receipt of your report
2. **Investigation**: We'll investigate and validate the vulnerability
3. **Fix Development**: We'll develop and test a fix
4. **Disclosure**: We'll coordinate disclosure with you
5. **Release**: We'll release a security patch
6. **Credit**: We'll credit you in the release notes (unless you prefer to remain anonymous)

## Security Best Practices

When using Stub Generator:

### 1. Keep Updated

Always use the latest version:

```bash
composer update pixielity/stub-generator
```

### 2. Validate Input

When accepting user input for stub paths or placeholders, always validate and sanitize:

```php
// Bad - User input directly used
$stub = StubGenerator::create($_GET['template']);

// Good - Validate against whitelist
$allowedTemplates = ['user', 'post', 'comment'];
$template = in_array($_GET['template'], $allowedTemplates) 
    ? $_GET['template'] 
    : 'default';
$stub = StubGenerator::create($template);
```

### 3. Restrict Stub Directory

Use `setBasePath()` to restrict stub loading to a specific directory:

```php
// Restrict to specific directory
StubGenerator::setBasePath('/path/to/safe/stubs');

// Now all stubs must be in this directory
$stub = StubGenerator::create('template.stub');
```

### 4. Sanitize Placeholders

When using user input as placeholder values, sanitize appropriately:

```php
// Bad - User input directly used
$content = StubGenerator::create('template.stub', [
    'name' => $_POST['name'],
])->render();

// Good - Sanitize input
$content = StubGenerator::create('template.stub', [
    'name' => htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'),
])->render();
```

### 5. File Permissions

Ensure proper file permissions on stub directories:

```bash
# Stub directory should be readable but not writable by web server
chmod 755 /path/to/stubs
chmod 644 /path/to/stubs/*.stub
```

### 6. Review Dependencies

Regularly audit dependencies for known vulnerabilities:

```bash
composer audit
```

## Known Security Considerations

### Path Traversal

The library validates file paths to prevent path traversal attacks. However, always:

- Use `setBasePath()` to restrict stub directory
- Validate user input before using as stub paths
- Never allow arbitrary file paths from user input

### Template Injection

When using user input as placeholder values:

- Sanitize input appropriately for the output format
- HTML: Use `htmlspecialchars()`
- SQL: Use prepared statements
- Shell: Use proper escaping

### File System Access

The library has access to the file system with PHP process privileges:

- Restrict stub directory permissions
- Don't store sensitive data in stub files
- Use appropriate file permissions

## Security Features

### Input Validation

- File paths are validated and sanitized
- Relative paths are resolved safely
- Directory traversal attempts are blocked

### Safe Defaults

- Base path defaults to safe location
- File operations use secure methods
- Exceptions provide helpful but not sensitive information

### Error Handling

- Exceptions don't expose sensitive paths
- Error messages are user-friendly
- Stack traces don't leak sensitive information

## Disclosure Policy

When a security vulnerability is fixed:

1. We'll release a patch version
2. We'll publish a security advisory
3. We'll update CHANGELOG.md with security notice
4. We'll credit the reporter (unless anonymous)

## Security Hall of Fame

We appreciate security researchers who help keep Stub Generator secure. Contributors will be listed here:

- (No vulnerabilities reported yet)

## Questions?

For security-related questions that are not vulnerabilities, you can:

- Open a discussion in the repository
- Contact the maintainers
- Review the documentation

Thank you for helping keep Stub Generator secure!
