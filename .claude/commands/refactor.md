---
description: Refactor messy code without changing behavior
---

The code at the file I'll mention works but is messy.

Refactor it with these goals:

- Extract business logic to a Service class in `app/Services/`
- Use typed properties and return types
- Remove code smells (long methods, nested ifs, magic numbers)
- Keep all existing tests passing
- Keep the public interface (method signatures) identical

Do NOT change behavior. Only structure.

Show me a clear before/after diff.
Run the existing tests after refactoring to confirm nothing broke.
