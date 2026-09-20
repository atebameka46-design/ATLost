# Fix conversations migration foreign key constraints

## Root cause
`database/migrations/2026_09_19_214631_create_conversations_table.php` uses `constrained()` without specifying the target table. Laravel infers the table name from the foreign key column:
- `user_two_id` → `user_twos` (does not exist)
- `user_one_id` → `user_ones` (does not exist)

This causes the `INSERT` on `conversations` to fail with `no such table: main.user_twos`.

## Steps
1. Edit `database/migrations/2026_09_19_214631_create_conversations_table.php`
   - Change line 13: `constrained()` → `constrained('users')`
   - Change line 14: `constrained()` → `constrained('users')`
2. Rollback the migration:
   - `php artisan migrate:rollback --path=database/migrations/2026_09_19_214631_create_conversations_table.php`
3. Re-run the migration:
   - `php artisan migrate --path=database/migrations/2026_09_19_214631_create_conversations_table.php`
4. Verify the fix by hitting `/messages?document=7`.

## Alternative (if data exists)
If the `conversations` table already contains data, create a new migration that drops the bad foreign keys and recreates them pointing to `users` using raw SQL / `dropForeign` + `foreign(...)->references('id')->on('users')`.
