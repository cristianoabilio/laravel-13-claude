---
description: Audit the most recent code changes for security vulnerabilities
---

Review the code I just committed in the most recent commit (or the staged
changes if nothing is committed yet).

Act as a senior Laravel security auditor. Check for:

1. SQL injection — any raw queries or unbinded inputs
2. XSS — any unescaped Blade output (`{!! !!}` without sanitization)
3. CSRF — missing tokens on state-changing routes
4. Mass assignment — missing `$fillable` or `$guarded` on models
5. Authorization bypass — missing middleware or policy checks
6. IDOR (Insecure Direct Object Reference) — can a patient access another patient's data?
7. Rate limiting — should this endpoint have throttling?
8. Sensitive data leaks — passwords, tokens, or PII in logs/responses

For every finding, output:

- **Severity**: CRITICAL / HIGH / MEDIUM / LOW
- **File:Line**: exact location
- **Issue**: what's wrong
- **Fix**: actual code to replace it

Be paranoid. In healthcare, one leak = game over.
