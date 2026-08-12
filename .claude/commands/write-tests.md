---
description: Generate comprehensive Pest tests for a class or feature
---

Generate comprehensive Pest tests for the file I mention or the most recent
file we worked on.

Structure:

- Feature tests for HTTP routes/controllers
- Unit tests for service methods
- Use datasets for validation rules with multiple cases
- Mock external services (Stripe, Mail, Storage)

Every test must:

- Have a descriptive name using `it('does something specific')`
- Cover the happy path
- Cover at least 2 edge cases
- Cover at least 1 failure case (validation, auth, etc.)

Use the existing patterns in `tests/` folder if any tests already exist.
Output: a single test file, ready to run with `php artisan test`.

Show me the file path you'll create. Wait for my confirmation before writing.
