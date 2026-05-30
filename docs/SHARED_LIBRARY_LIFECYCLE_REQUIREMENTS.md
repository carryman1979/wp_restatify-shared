# Shared Library Lifecycle Requirements

Scope: `wp_restatify-booking`, `wp_restatify-ai-multichat`, `wp-restatify-forms`

This document defines the non-optional runtime and packaging rules for the versioned shared PHP library.

## Mandatory rules

1. On plugin install/update, the plugin MUST ensure the requested shared version is installed to:
   - `wp-content/wp_restatify-shared/versions/<shared-version>`
2. Runtime loading MUST use the exact requested version path (exact match, no implicit fallback to another version).
3. Each plugin release package MUST include its required shared version payload under a plugin-local install path.
4. If the requested version is missing centrally, the installer MUST copy the shared payload from the plugin package into the central version path.
5. Older installed shared versions MUST remain in place while any active plugin still depends on them.
6. On each install/update check, plugins MUST remove shared versions that are no longer required by any active plugin.
7. Production runtime MUST NOT depend on pulling shared files from external services.

## Why this exists

A previous deployment failed with a fatal runtime error because one plugin required a newly introduced shared file that was not present in the global shared path. These rules prevent that class of outage.
