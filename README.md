# Office Building Rental Management System

![Lint](https://github.com/2026pgaycl/combo/actions/workflows/lint.yml/badge.svg)

A plain-PHP MVC app for managing an office building rental portfolio — buildings, floors, units, tenants, leases, invoicing, maintenance tickets, document attachments, and a notification log. No Composer dependency, so it's deployable as-is on plain cPanel shared hosting.

## Local setup

1. Copy `.env.example` to `.env` and fill in your database credentials.
2. Import the schema and seed data:
   ```
   mysql -u root -p your_db_name < database/schema.sql
   mysql -u root -p your_db_name < database/seed.sql
   ```
3. Point your web server's document root at `public/`.
4. Log in with the seeded account: `admin@example.com` / `password` — change this immediately in production.

## Contributing

`main` is protected: changes go through a pull request, and the `php-lint` check (a `php -l` syntax pass over every file) must succeed before it merges. Auto-merge is enabled, so an approved PR with a green check merges on its own.
