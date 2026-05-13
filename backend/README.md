# IronPulse backend (PHP 8 + MySQL)

## Requirements

- PHP 8.0+ with extensions: `pdo_mysql`, `json`, `mbstring`, `fileinfo`, `session`
- MySQL 8+ (MariaDB 10.5+ compatible)
- Apache with `mod_rewrite` optional; PHP must execute under `/backend/api/`

## Database setup

1. Create database and import schema:

   ```sql
   CREATE DATABASE ironpulse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   ```bash
   mysql -u root -p ironpulse < backend/schema.sql
   mysql -u root -p ironpulse < backend/seed.sql
   ```

2. Configure credentials in `backend/config/database.php` or set environment variables:

   - `IRONPULSE_DB_HOST` (default `127.0.0.1`)
   - `IRONPULSE_DB_PORT` (default `3306`)
   - `IRONPULSE_DB_NAME` (default `ironpulse`)
   - `IRONPULSE_DB_USER` (default `root`)
   - `IRONPULSE_DB_PASS` (default empty for XAMPP)

## XAMPP / Apache layout

Point your browser at the project (for example `http://localhost/IRONPULSE/html/home.html`).

- Frontend resolves API URLs relative to `js/utils/paths.js` → `../../backend/api/...`.
- If the app lives in a subdirectory, set in the browser console or a small inline script:

  ```js
  window.__IRONPULSE_API_BASE__ = "http://localhost/IRONPULSE/backend/api/";
  ```

  Or set `IRONPULSE_URL_PREFIX` in PHP env to match the path prefix (e.g. `/IRONPULSE`).

Ensure `backend/uploads/` and `backend/uploads/avatars/` are writable by Apache.

## Demo account (from seed)

- **Email:** `demo@ironpulse.com`
- **Password:** `password`

If login fails, regenerate a bcrypt hash:

```bash
php backend/tools/hash_password.php "YourNewPassword"
```

Paste the output into `users.password_hash` for the demo row (or update `seed.sql`).

## Security notes

- Sessions use HTTP-only cookies; CSRF is required on mutating requests (except register/login).
- Use HTTPS in production and set `IRONPULSE_ENV=production` in `config.php` env for secure cookies.

See [API.md](../API.md) for REST endpoints.
