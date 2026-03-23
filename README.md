# BimmerTech Firmware Download Manager

## Quick Start

```bash
composer install
composer setup
composer serve
```

The app is now running at http://localhost:8000.

## System Requirements

- PHP 8.2+
- Composer
- SQLite extension for PHP (usually included by default)
- PHP-CS-Fixer (download from https://cs.symfony.com/download/php-cs-fixer-v3.phar and save as `php-cs-fixer` in project root)

To use MySQL/PostgreSQL instead of SQLite, change `DATABASE_URL` in `.env`.

**Note:** Run `composer install` (not `composer install --no-dev`) for initial setup to include fixtures.

## Composer Scripts

| Command | Description |
|---------|-------------|
| `composer setup` | Create database, run migrations, and load fixtures |
| `composer serve` | Start development server on localhost:8000 |
| `composer test` | Run PHPUnit tests |
| `composer lint` | Run PHPStan static analysis |
| `composer fix` | Fix code style issues with PHP-CS-Fixer |
| `composer analyse` | Run PHPStan analysis (alias for lint) |
| `composer qa` | Run all QA checks (tests, lint, code style check) |

## Usage

### Customer Page
Visit: http://localhost:8000/carplay/software-download

Enter a Software Version and HW Version, then click "Check" to see if a firmware update is available.

### Admin Panel
Visit: http://localhost:8000/admin

**Note:** No authentication is configured for the admin panel in this demo. In production, add Symfony Security to protect `/admin`.

### Managing Software Versions (Admin Panel)
1. Click **"Software Versions"** in the sidebar
2. To **add** a new version: click "Add Software Version"
   - Select the product name from the dropdown
   - Enter the version with `v` prefix in "Version (display)" — e.g. `v3.4.5.mmipri.c`
   - Enter the version without `v` prefix in "Version (lookup)" — e.g. `3.4.5.mmipri.c`
   - Paste the Google Drive download links for ST and/or GD variants (leave blank if not applicable)
   - If this is the newest version, check **"Latest Version"** and enter the display version (e.g. `v3.4.5`)
   - **IMPORTANT:** When marking a new version as "Latest", un-mark the previous latest version for the same product line
3. To **edit**: click on any row
4. To **delete**: select rows and use bulk delete
5. Use the **search bar** and **filters** (by product name, by latest status) to find entries

## Configuration

The shop URL is configurable via the `SHOP_URL` environment variable in `.env`:
```
SHOP_URL="https://your-shop-url.com/"
```

## Testing

Run the test suite:
```
php bin/phpunit
```

## API

The API endpoint `POST /api/carplay/software/version` expects a JSON body:

```json
{
    "version": "3.1.1.2.mmi.c",
    "mcuVersion": "",
    "hwVersion": "CPAA_2024.01.15"
}
```

The `Content-Type` header must be `application/json`.
