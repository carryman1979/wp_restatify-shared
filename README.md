# wp_restatify-shared

Public shared package for Restatify WordPress plugins.

Version: 1.0.1

License: GPL-2.0-or-later
Website: https://www.restatify.tech

## Purpose

This package provides versioned shared runtime utilities for Restatify plugins.

Key rule:

- Share only on exact version match.
- If versions differ, each plugin loads its own compatible shared bundle.

## Components

- PHP runtime registry: `src/php/SharedRegistry.php`
- PHP migration notice flow: `src/php/Migration/MigrationNoticeManager.php`
- JS runtime registry: `src/js/runtime-registry.js`

## Versioning policy

- Semantic Versioning (SemVer)
- No implicit cross-major compatibility
- Plugins must request exact shared version

## Distribution

- Public repository
- Composer package metadata included
- npm package metadata included

## Migration notice flow

`MigrationNoticeManager` can be used by renamed target plugins after a successful settings migration.

Behavior:

- shows DE/EN admin notice (DE default)
- default action is keep legacy plugins
- optional action deactivates and removes legacy plugins
- includes warning that logs/history are not migrated

## Release 1.0.1 highlights

- Added shared AI guideline source files for cross-repo AGENTS and Copilot instructions.
- Standardized central documentation paths for multi-repo development workflows.
