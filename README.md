# BimmerTech Firmware Download Manager

## Quick Start

```bash
cp .env.example .env        # Copy environment config (edit if needed)
composer install
composer setup
php -S localhost:8000 -t public
```

The app is now running at http://localhost:8000.

- Customer page: http://localhost:8000/carplay/software-download
- Admin panel: http://localhost:8000/admin (login: `admin` / `admin123`)

## System Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ (default) or SQLite / PostgreSQL

**Note:** Run `composer install` (not `--no-dev`) for initial setup to include fixtures.

## Environment Configuration

Copy `.env.example` to `.env` and adjust as needed. All configurable values:

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_ENV` | `dev` | Environment (`dev`, `prod`, `test`) |
| `APP_SECRET` | — | Symfony secret key (change in production) |
| `DB_DRIVER` | `pdo_mysql` | Database driver (`pdo_mysql`, `pdo_pgsql`) |
| `DB_HOST` | `127.0.0.1` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_NAME` | `firmware` | Database name |
| `DB_USER` | `root` | Database username |
| `DB_PASSWORD` | *(empty)* | Database password |
| `DB_VERSION` | `8.0` | Database server version |
| `SHOP_URL` | BimmerTech shop URL | "Go back to the shop" link target |
| `ADMIN_USERNAME` | `admin` | Admin panel login username |
| `ADMIN_PASSWORD` | *(bcrypt hash)* | Admin panel login password (hashed) |

### Changing the Admin Password

```bash
# Generate a new password hash
php bin/console security:hash-password 'your_new_password'

# Copy the hash and set it in .env.local (not .env, to avoid committing secrets)
ADMIN_USERNAME=your_username
ADMIN_PASSWORD='$2y$13$your_generated_hash_here'
```

### Database Options

MySQL (default):
```
DB_DRIVER=pdo_mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=firmware
DB_USER=root
DB_PASSWORD=
DB_VERSION=8.0
```

PostgreSQL:
```
DB_DRIVER=pdo_pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=firmware
DB_USER=postgres
DB_PASSWORD=
DB_VERSION=16
```

## Composer Scripts

| Command | Description |
|---------|-------------|
| `composer setup` | Download php-cs-fixer, create database, run migrations, load fixtures |
| `composer test` | Run PHPUnit tests |
| `composer lint` | Run PHPStan static analysis |
| `composer fix` | Fix code style issues with PHP-CS-Fixer |
| `composer qa` | Run all checks: tests, PHPStan, code style |

## Admin Panel

Visit: http://localhost:8000/admin

Protected with form login. Default credentials: `admin` / `admin123`.

### Managing Software Versions

1. Click **"Software Versions"** in the sidebar
2. To **add** a new version: click "Add Software Version"
   - Select the product name from the dropdown
   - Enter the version with `v` prefix in "Version (display)" — e.g. `v3.4.5.mmipri.c`
   - Enter the version without `v` in "Version (lookup)" — e.g. `3.4.5.mmipri.c`
   - Paste the Google Drive download links for ST and/or GD variants (leave blank if not applicable)
   - If this is the newest version, check **"Latest Version"** and enter the display version (e.g. `v3.4.5`)
   - **IMPORTANT:** When marking a new version as "Latest", un-mark the previous latest version for the same product line
3. To **edit**: click on any row
4. To **delete**: select rows and use bulk delete
5. Use the **search bar** and **filters** (by product name, by latest status) to find entries

## API

`POST /api/carplay/software/version`

```json
{
    "version": "3.1.1.2.mmi.c",
    "mcuVersion": "",
    "hwVersion": "CPAA_2024.01.15"
}
```

The `Content-Type` header must be `application/json`. The API is public — no authentication required.

## Testing

```bash
composer test          # PHPUnit (16 tests, 42 assertions)
composer lint          # PHPStan level 6
composer qa            # All checks
```

## Code Quality

- **PHPStan** level 6 with Symfony + Doctrine extensions
- **PHP-CS-Fixer** with PSR-12 ruleset
- **PHPUnit** functional tests for all API scenarios
