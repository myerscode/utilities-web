# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Changed
- Upgrade minimum PHP version to ^8.5
- Modernise codebase with Rector (strict types, typed properties, yield data providers, first-class callables)
- Update php-curl-class to ^12.0, Symfony packages to ^8.0, PHPUnit to ^13.0
- Fix broken `UriUtility::check()` method to delegate to `ResponseUtility`
- Fix `getQueryParameters()` null safety for `parse_url` return
- Add type hints to `getQueryString()` parameters
- Fix `ResponseUtility::setUrl()` to properly handle `UriUtility` parameter

### Added
- PHPStan static analysis at level 8
- Laravel Pint code style enforcement
- Security audit CI workflow
- Dependabot configuration for automated dependency updates
- PHP version badge and Requirements section in README

### Removed
- `InvalidQueryParamsException` (never thrown)
- `InvalidSchemeException` (never thrown)
- `squizlabs/php_codesniffer` (replaced by Laravel Pint)

## [1.3.1](https://github.com/myerscode/utilities-web/releases/tag/1.3.1) - 2019-05-24

- [`6fc57fa`](https://github.com/myerscode/utilities-web/commit/6fc57fad0e74ee7d1d3bd7b11525f2c4cbcddbda) feat: added method to added query params to url
- [`6f7f5ff`](https://github.com/myerscode/utilities-web/commit/6f7f5ff7f1df146ba35123a795a81479130d8e9b) chore: updated code coverage properties
- [`6196f66`](https://github.com/myerscode/utilities-web/commit/6196f660210c77d0bf01f1a08077a2b7251f8b1f) chore: bumped dependancy versions
- [`8e00d3f`](https://github.com/myerscode/utilities-web/commit/8e00d3f6da9e9a906b8f831e21221d365724f023) test: fixed failing test for exception checking
- [`bcab932`](https://github.com/myerscode/utilities-web/commit/bcab932affd465d59d316852a0ed51ee980c263c) chore: removed reference to url utility
