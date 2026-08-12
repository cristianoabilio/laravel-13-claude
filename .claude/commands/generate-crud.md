---
description: Generate a complete CRUD module for a given model name
---

Generate a complete CRUD module for the model name I provide.

Before writing any code, list:

1. The migration file you'll create
2. The model file with relationships
3. The controller (resource controller)
4. Form Request classes (Store + Update)
5. Routes to add
6. Blade views (index, create, edit, show)
7. Pest tests

Wait for my "go" before writing any files.

Follow CLAUDE.md rules strictly:

- Controllers in correct role folder (Admin/Doctor/Patient/Public)
- Business logic in Service classes, NOT controllers
- Form Requests for ALL validation
- Use the Doccure HTML template for all views
- Add proper authorization middleware
