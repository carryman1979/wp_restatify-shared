# Shared Agent Workflow (Restatify)

## Purpose
This is the canonical, shared workflow for coding agents across Restatify repositories.

## Non-Negotiables
- Preserve backward compatibility unless a change is explicitly planned and documented.
- Keep security checks intact (nonce, capability, validation, sanitization).
- Do not mask runtime failures as success.
- Keep changes focused; avoid unrelated refactors.
- Add or update tests for every bugfix.
- Never commit secrets, production tokens, or operational deployment internals to tracked files.

## Required Delivery Standard
- Make the smallest safe change that resolves the issue.
- Run relevant unit tests before completion.
- If behavior changes intentionally, update changelog/docs in the same change.
- Prefer deterministic test commands over ad-hoc manual checks.
