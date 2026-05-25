# Shared Copilot Instructions (Restatify)

## Goal
Protect production behavior while enabling fast, safe iteration across Restatify codebases.

## Hard Rules
- Keep backward compatibility for existing public behavior unless explicitly changed by requirement.
- Preserve nonce/security/capability checks and input sanitization.
- Do not swallow critical failures silently.
- Avoid broad refactors in bugfix tasks.
- Do not commit secrets, API keys, private tokens, or operational runbook internals.

## Shared Loader Policy
- Shared resolution order must be deterministic: prefer local root shared without version folder first (`.../wp_restatify-shared/src/*`) for local development.
- If local root shared is not available, load only the required released shared version from `wp-content/plugins/wp_restatify-shared/versions/<x.y.z>/` (or mu-plugins equivalent).
- Do not mix local root shared and versioned plugin shared in the same request.
- Each plugin or theme must request and load its exact required shared version when using versioned shared.

## Required Checks Before Finishing
- Run relevant unit tests for changed areas.
- If trigger/link behavior is changed, validate trigger parsing and persisted values.
- If API/request mapping is changed, verify input/output mapping remains stable.
- If a chat handover opens another workflow, document the token/event contract and prefilling fields shared between repos.

## Editing Guidelines
- Prefer small, targeted diffs.
- Keep editor/runtime behavior consistent.
- Add or update tests with each bugfix.
