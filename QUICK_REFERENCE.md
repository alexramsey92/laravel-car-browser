# Quick Reference Guide

## Running the Application

### Start Local Server
```bash
cd c:\Users\alexr\Herd\laravel-car-browser
php artisan serve
```
Server runs at: `http://localhost:8000`

## Accessing the Web Interface

### Public Pages
### Admin Login

The seeder now generates a random admin password at seed time. Run `php artisan db:seed` and check the console output for the admin credentials, or create your own admin account via registration or `php artisan tinker`.

### Admin Dashboard
- **Admin Home** → `http://localhost:8000/admin`
- **Admin Car List** → `http://localhost:8000/admin/cars`
- **Admin Scraper** → `http://localhost:8000/admin/scrape`

### Login Credentials
```
Email: admin@example.com
Password: password
```

## Command Line Operations

### List Available Scrapers
```bash
php artisan cars:scrape --list
```

### Test Scraper (Demo)
```bash
php artisan cars:scrape --source=test
```

### Real Scraper (Ford F-150s)
```bash
# Dry run (preview only)
php artisan cars:scrape --source=cars-real --no-save

# Save to database
php artisan cars:scrape --source=cars-real

# Run and display results
php artisan cars:scrape --source=cars-real --no-save
```

### Run All Scrapers
```bash
php artisan cars:scrape
```

### Quick Test
```bash
php artisan cars:scrape --test
```

## Database Operations

### Reset Database
```bash
php artisan migrate:refresh --seed
```
This clears all tables and recreates them.

### Check Database Contents
```bash
# Count total cars
php -r "require 'vendor/autoload.php'; \$pdo = new PDO('sqlite:database/database.sqlite'); echo 'Total: ' . \$pdo->query('SELECT COUNT(*) FROM cars')->fetch()[0];"

# Show sample cars
php -r "require 'vendor/autoload.php'; \$pdo = new PDO('sqlite:database/database.sqlite'); foreach(\$pdo->query('SELECT * FROM cars LIMIT 3')->fetchAll() as \$c) echo \$c['year'].' '.\$c['make'].' '.\$c['model'].\" - \\\$\".\$c['price'].\"\\n\";"
```

### Clear Tables
```bash
php artisan tinker
>>> DB::table('cars')->truncate();
>>> exit;
```

## Troubleshooting

### Cache Path Error
```bash
bash setup.sh
# or manually create directories
mkdir -p storage/framework/{cache/data,views,sessions}
```

### Database Locked
```bash
php artisan cache:clear
php artisan config:cache
```

### Migrations Failed
```bash
php artisan migrate:reset
php artisan migrate
php artisan seed
```

### No Cars Showing
1. Run scraper: `php artisan cars:scrape --source=cars-real`
2. Check database: `php artisan tinker` → `DB::table('cars')->count()`
3. Clear cache: `php artisan cache:clear`
4. Refresh browser: `Ctrl+Shift+R`

### Scraper Returns 0 Cars
This is normal - Cars.com blocks automated access, so fallback data is used.
The fallback generates 10-16 realistic Ford F-150 listings.

## File Locations

| File | Purpose |
|------|---------|
| `app/Services/Scraper/ScraperManager.php` | Main orchestrator |
| `app/Services/Scraper/BaseScraper.php` | Base scraper class |
| `app/Services/Scraper/Scrapers/TestScraper.php` | Demo scraper |
| `app/Services/Scraper/Scrapers/RealCarsComScraper.php` | Real scraper |
| `app/Http/Controllers/AdminController.php` | Admin routes |
| `app/Http/Controllers/CarController.php` | Car routes |
| `resources/views/admin/scrape.blade.php` | Scraper UI |
| `resources/views/cars/index.blade.php` | Car listing page |
| `database/database.sqlite` | SQLite database |

## Environment Setup

### Installation (First Time)
```bash
cd c:\Users\alexr\Herd\laravel-car-browser

# Copy environment file
copy .env.example .env

# Install dependencies (if needed)
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Setup directories
bash setup.sh
```

### Daily Usage
```bash
# Terminal 1: Start server
php artisan serve

# Terminal 2: Run scrapers (optional)
php artisan cars:scrape --source=cars-real

# Then open browser to http://localhost:8000
```

## Common Workflows

### Workflow 1: View Current Cars
```
1. Browser → http://localhost:8000/cars
2. See all scraped Ford F-150s
3. Click on any car for details
```

### Workflow 2: Scrape New Cars
```
1. Browser → http://localhost:8000/admin/scrape
2. Check "cars-real" checkbox
3. Click "Start Scraper"
4. Wait for completion
5. Browser → http://localhost:8000/cars
6. See newly scraped cars
```

### Workflow 3: Full Refresh
```bash
php artisan migrate:refresh --seed
php artisan cars:scrape --source=cars-real
# Then view at http://localhost:8000/cars
```

### Workflow 4: CLI Dry Run
```bash
php artisan cars:scrape --source=cars-real --no-save
# Shows preview without saving to DB
# Useful for testing or checking data
```

## API Reference (CLI)

### cars:scrape Command

**Usage:**
```bash
php artisan cars:scrape [options]
```

**Options:**
```
--source=NAME       Run specific scraper (test, cars-real)
--list              List available scrapers
--no-save           Dry run (don't save to database)
--test              Quick test mode
--help              Show help message
```

**Examples:**
```bash
php artisan cars:scrape --list
php artisan cars:scrape --source=test
php artisan cars:scrape --source=cars-real --no-save
php artisan cars:scrape --source=cars-real
php artisan cars:scrape
```

## Performance Tips

### Faster Scraping
- Dry run first: `--no-save`
- Use specific source: `--source=cars-real`
- Check database before scraping: `php artisan tinker` → `DB::table('cars')->count()`

### Faster Page Load
- Clear cache: `php artisan cache:clear`
- Check images load from Unsplash (reliable)
- Use browser dev tools (F12) to check network tab

### Database Performance
- Check index: `SELECT * FROM cars WHERE year = 2022;`
- Monitor log: `tail -f storage/logs/laravel.log`

## Useful Laravel Commands

```bash
# Serve application
php artisan serve

# Interactive shell
php artisan tinker

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Show routes
php artisan route:list

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Run tests
php artisan test
```

## Documentation Files

| File | Content |
|------|---------|
| `README.md` | Getting started, setup instructions |
| `STATUS.md` | Current project status |
| `SCRAPER_HYBRID_APPROACH.md` | How hybrid scraper works |
| `SCRAPER_ARCHITECTURE.md` | System architecture details |
| `this file` | Quick reference guide |

## Key Concepts

### What is a Scraper?
A scraper fetches data from websites and extracts useful information (like car listings).

### What is Hybrid Approach?
1. **Try** to get real data from Cars.com
2. **If blocked/timeout:** Use realistic generated data
3. **Result:** Always shows something, never empty

### What is Fallback?
Fallback data is generated when live scraping fails. It's realistic but not from actual Cars.com.

### What is Dry Run?
A dry run previews scraped data without saving to database. Useful for testing.

## Support

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Issues
```bash
php artisan migrate:refresh --seed
```

### Application Issues
```bash
php artisan cache:clear
php artisan config:cache
php artisan view:clear
```

### Full Reset
```bash
php artisan migrate:reset
php artisan migrate
php artisan db:seed
bash setup.sh
```

---

**Last Updated:** Current session
**Version:** 1.0
