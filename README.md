# signup-php

A minimal PHP + MySQL example for user signup (registration) and basic account handling.

This repository demonstrates a small signup flow using PHP and MySQL with secure password storage and prepared statements. It is intended as a starting point for learning, prototypes, and small projects — not a production-ready identity system.

## Contents

- `public/` — public web root (example: `index.php`, `register.php`, `login.php`).
- `src/` — PHP source code and helpers (database connection, user model).
- `config.php` — simple configuration file for DB connection (or use `.env`).
- `sql/` — example SQL schema to create the `users` table.

> If your repo layout differs, adjust the paths above.

## Requirements

- PHP 8.0 or newer
- MySQL (or MariaDB)
- A web server (Apache/IIS) or PHP built-in server for development

## Quick setup (development)

1. Copy or create configuration for database connection. If the project uses `config.php`, edit it with your DB credentials. If using environment variables, create a `.env` file accordingly.

2. Create the database and `users` table. Example SQL (run in MySQL):

```sql
CREATE DATABASE IF NOT EXISTS signup;
USE signup;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

3. Start the built-in PHP server for local testing (from project root):

```powershell
# serve the public directory on localhost:8000
php -S localhost:8000 -t public
```

4. Open http://localhost:8000 in your browser and use the registration form.

## Security notes (important)

- Always use prepared statements (PDO or mysqli with parameter binding) to prevent SQL injection.
- Store passwords using `password_hash()` and verify with `password_verify()`.
- Use HTTPS in production.
- Properly validate and sanitize user inputs (server-side validation is required).
- Protect against CSRF on forms (tokens) and use secure session handling.

## Recommended configuration

- Move DB credentials out of source; use environment variables or a config file excluded from version control.
- Add `config.php` to `.gitignore` if it contains secrets.

## File examples

- `public/register.php` — registration form and handler.
- `src/db.php` — returns a PDO/MySQL connection using config settings.
- `src/User.php` — user-related helpers (create, find, verify).
- `sql/schema.sql` — contains the SQL from this README.

## Troubleshooting

- "Access denied" / authentication errors: double-check DB host, username, password, and that the DB user has privileges.
- Blank pages / PHP errors: enable display_errors in development or check your web server / PHP error logs.
- Port conflicts when running `php -S`: use a different port (e.g., `localhost:8080`).

## Next steps (suggested)

- Add a `.env` example and a loader (e.g., vlucas/phpdotenv).
- Add a small migration script to create the `users` table automatically.
- Add unit/integration tests around registration and login flows.
- Harden sessions, add account verification (email), rate-limiting, and logging.

## License

Choose an appropriate license (e.g., MIT) and include a `LICENSE` file if you plan to publish this.

---

If you want, I can:
- add a `.env.example` and `.gitignore`,
- create `sql/schema.sql` with the schema above,
- or scaffold a minimal `public/register.php` and `src/db.php` so it runs out-of-the-box. Which would you like next?