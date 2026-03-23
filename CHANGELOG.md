# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [CalVer](https://calver.org/) (`YYYY.MINOR.PATCH`).

## Unreleased

### Changed
- Minimum PHP version ^8.5
- Replace `METHOD_*` constants with `ResponseFrom` enum in `UriUtility::check()`
- Consolidate HTTP client creation in `ClientUtility`
- Modernise codebase with Rector (strict types, typed properties, coding style)
- Bump PHPStan to level 9
- Harden phpunit.xml with failOnRisky and failOnWarning
- Standardise GitHub Actions workflows (checkout v6, codecov v5)
- Standardise dev tooling (Pint config with single_quote, trailing_comma, no_unused_imports)
- Update documentation for all utilities
- Switch versioning to CalVer (YYYY.MINOR.PATCH)

### Added
- Comprehensive test suite (149 tests, 92.93% line coverage)
- Tests for Utility, ClientUtility, ResponseFrom, Response resource
- Tests for ResponseUtility construct, check, timeout, follow redirects, validation
- Tests for ContentUtility response exception paths, redirect handling
- Tests for UriUtility scheme, path, query, aliases, check
- Composer `ci` and `rector` scripts
- Release and update-changelog GitHub Actions workflows
- Dependabot configuration

### Fixed
- `UriUtility::getQueryParameters()` bug where `parse_url` on bare query string returned null

### Removed
- `CurlInitException` (never thrown)
- Unused `$requestOptions` from `Utility`, `ClientUtility`, `ContentUtility`
- Duplicate `client()` method from `Utility` (consolidated in `ClientUtility`)
- `ext-sockets` from production requirements (only needed by dev deps)
- Dead commented-out code and stale docblocks
- Static analysis CI workflow (consolidated into standard tooling)

## [1.3.1](https://github.com/myerscode/utilities-web/releases/tag/1.3.1) - 2019-05-24

- [`6fc57fa`](https://github.com/myerscode/utilities-web/commit/6fc57fad0e74ee7d1d3bd7b11525f2c4cbcddbda) feat: added method to added query params to url
- [`6f7f5ff`](https://github.com/myerscode/utilities-web/commit/6f7f5ff7f1df146ba35123a795a81479130d8e9b) chore: updated code coverage properties
- [`6196f66`](https://github.com/myerscode/utilities-web/commit/6196f660210c77d0bf01f1a08077a2b7251f8b1f) chore: bumped dependancy versions
- [`8e00d3f`](https://github.com/myerscode/utilities-web/commit/8e00d3f6da9e9a906b8f831e21221d365724f023) test: fixed failing test for exception checking
- [`bcab932`](https://github.com/myerscode/utilities-web/commit/bcab932affd465d59d316852a0ed51ee980c263c) chore: removed reference to url utility
