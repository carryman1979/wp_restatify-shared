# Release 1.1.0

- Add `Security/CaptchaVerifier` for server-side reCAPTCHA v3 and Cloudflare Turnstile verification.
- Fail verification on transport errors, non-200 responses, invalid JSON, missing credentials/tokens, insufficient scores or mismatched requested actions.
- Backward-compatible additive minor release; PHP 8.0+ remains required.
- Downstream releases: Restatify Base 1.2.0 and Restatify Forms 1.1.0 require exact shared version 1.1.0 and bundle its PHP payload.
- Historical metadata differs: the published GitHub release is 1.0.3, while main's npm metadata was 1.0.2. This release aligns the current metadata at 1.1.0 without rewriting historical tags.

## Packaging

Use the existing shared archive workflow from the repository root:

```powershell
git archive --format=zip --prefix=wp_restatify-shared/ --output=release/wp_restatify-shared-1.1.0.zip HEAD . ':!release'
```

Build after committing the source and metadata, before adding the new archive. Inspect it for `wp_restatify-shared/src/php/Security/CaptchaVerifier.php`.
