# wp_restatify-shared

Public shared package for Restatify WordPress plugins.

Version: 1.0.2

License: GPL-2.0-or-later
Website: https://www.restatify.tech

## Purpose

This package provides versioned shared runtime utilities for Restatify plugins.

Key rule:

- Share only on exact version match.
- If versions differ, each plugin loads its own compatible version from the central shared store.

## Components

- PHP runtime registry: `src/php/SharedRegistry.php`
- PHP migration notice flow: `src/php/Migration/MigrationNoticeManager.php`
- API error formatting helper: `src/php/Api/BookingApiErrorFormatter.php`
- Shared overlay CSS utility: `src/css/overlay-window.css`
- JS runtime registry: `src/js/runtime-registry.js`

## Versioning policy

- Semantic Versioning (SemVer)
- No implicit cross-major compatibility
- Plugins must request exact shared version

## Shared library lifecycle requirement

This requirement is mandatory for `wp_restatify-booking`, `wp_restatify-ai-multichat`, and `wp-restatify-forms`:

- On install/update, the plugin must ensure this exact shared version is present under `wp-content/wp_restatify-shared/versions/<version>`.
- The plugin must load shared files from its exact requested version path.
- Each plugin release package must include a shared payload for its requested version.
- If the required version is missing centrally, the plugin must install it from its own packaged payload.
- Older shared versions must remain installed while at least one active plugin still requires them.
- During install/update checks, unused shared versions must be removed when no active plugin depends on them.
- Production runtime must not depend on external source downloads.

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

## Release 1.0.2 highlights

- Added `PrivacyLegalNotice` utility for centralized legal notice handling across plugins.
- Documented the release workflow and dependency update expectations for downstream repos.
- Hotfix cycle without version bump: existing dependent plugin/theme release assets were replaced to fix shared-library installation/runtime path resolution.

## Release-prep refresh (2026-05-30)

- No version bump: release prep remains on `1.0.2`.
- Added shared booking API error formatting helper used by dependent plugin test/runtime paths.
- Added shared overlay CSS utility for cross-repo UI consistency during hotfix rollout.
