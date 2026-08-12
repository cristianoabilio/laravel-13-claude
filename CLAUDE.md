## 🚫 Context Boundaries — Files Claude Must NEVER Touch

The following files are completely off-limits. Never read, modify, or delete them:

- `.env` and `.env.*` files (contain secrets)
- `vendor/` folder (managed by Composer)
- `node_modules/` folder (managed by NPM)
- `storage/app/` (real user uploads)
- `storage/logs/` (production logs)
- `bootstrap/cache/` (auto-generated)
- Any file matching `*.key` or `*.pem` (cryptographic keys)
- Any migration that has a timestamp older than [insert date you went to production]

## 🛑 Destructive Operations — Always Confirm First

NEVER execute these without explicit, in-the-moment permission from me:

- `php artisan migrate:fresh` (deletes all data)
- `php artisan migrate:rollback` past more than 1 step
- `DB::table()->truncate()` or `delete()` on more than 100 records
- `rm -rf` or any recursive deletion
- `composer remove` any package
- `npm uninstall` any package
- Any direct database modification on production
- Force-pushing to git (`git push --force`)
- Modifying webhook URLs in production Stripe

## ✅ Operations That Need Permission

Always ask before:

- Installing a new Composer or NPM package
- Adding a new database column to a table that already has production data
- Changing the API contract of any endpoint already deployed
- Modifying authentication logic
- Modifying payment logic
- Changing role/permission definitions
