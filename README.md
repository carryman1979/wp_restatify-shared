# wp_restatify-shared

Public shared package for Restatify WordPress plugins.

License: GPL-2.0-or-later
Website: https://www.restatify.tech

## Purpose

This package provides versioned shared runtime utilities for Restatify plugins.

Key rule:

- Share only on exact version match.
- If versions differ, each plugin loads its own compatible shared bundle.

## Components

- PHP runtime registry: `src/php/SharedRegistry.php`
- JS runtime registry: `src/js/runtime-registry.js`

## Versioning policy

- Semantic Versioning (SemVer)
- No implicit cross-major compatibility
- Plugins must request exact shared version

## Distribution

- Public repository
- Composer package metadata included
- npm package metadata included
