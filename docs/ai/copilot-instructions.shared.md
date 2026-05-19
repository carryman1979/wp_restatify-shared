# Shared Copilot Instructions (Restatify)

## Goal
Protect production behavior while enabling fast, safe iteration across Restatify codebases.

## Hard Rules
- Keep backward compatibility for existing public behavior unless explicitly changed by requirement.
- Preserve nonce/security/capability checks and input sanitization.
- Do not swallow critical failures silently.
- Avoid broad refactors in bugfix tasks.
- Do not commit secrets, API keys, private tokens, or operational runbook internals.

## Required Checks Before Finishing
- Run relevant unit tests for changed areas.
- If trigger/link behavior is changed, validate trigger parsing and persisted values.
- If API/request mapping is changed, verify input/output mapping remains stable.
- If a chat handover opens another workflow, document the token/event contract and prefilling fields shared between repos.

## Editing Guidelines
- Prefer small, targeted diffs.
- Keep editor/runtime behavior consistent.
- Add or update tests with each bugfix.
