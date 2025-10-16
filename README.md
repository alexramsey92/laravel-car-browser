# Laravel Car Browser

A Laravel application to scrape, aggregate, and list all available cars for sale from major car sales websites. Features an admin interface for managing car listings and running the scraper.

## Features

- **Car Aggregation**: Scrapes car listings from multiple major car sales websites:
  - AutoTrader
  - Cars.com
  - CarGurus
  - CarMax

- **Browse Cars**: Public interface to browse and filter available cars
  - Filter by make, model, year range, and price range
  - Detailed car information pages
  - Responsive design

- **Admin Interface**: 
  - Dashboard with statistics
  - Car management interface
  - Manual scraper execution
  - Admin user authentication

- **Automated Scraping**: Daily scheduled scraping via Laravel scheduler

## Installation

1. Clone the repository:
```bash
git clone https://github.com/alexramsey92/laravel-car-browser.git
cd laravel-car-browser
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Create database file (SQLite):
```bash
touch database/database.sqlite
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database with admin user:
```bash
php artisan db:seed
```

This will create an admin user with the following credentials:
- Email: `admin@example.com`
- Password: `password`

## Usage

### Running the Application

Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Scraping Cars

Run the scraper manually:
```bash
php artisan cars:scrape
```

Or use the admin interface at `/admin/scrape` to run the scraper.

### Admin Interface

Access the admin dashboard at `/admin` to:
- View statistics and recent listings
- Manage all car listings
- Run the scraper manually

### Scheduled Scraping

The scraper runs automatically once daily. To enable scheduled tasks, add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Project Structure

- **Models**: `app/Models/Car.php`, `app/Models/User.php`
- **Controllers**: `app/Http/Controllers/CarController.php`, `app/Http/Controllers/AdminController.php`
- **Services**: `app/Services/CarScraperService.php` - Main scraping logic
- **Commands**: `app/Console/Commands/ScrapeCars.php` - CLI command for scraping
- **Views**: 
  - `resources/views/cars/` - Public car browsing views
  - `resources/views/admin/` - Admin interface views
- **Migrations**: `database/migrations/` - Database schema
- **Seeders**: `database/seeders/AdminUserSeeder.php` - Admin user seeder

## Database Schema

### Cars Table
- `id` - Primary key
- `make` - Car manufacturer
- `model` - Car model
- `year` - Manufacturing year
- `price` - Listing price
- `mileage` - Odometer reading
- `color` - Exterior color
- `transmission` - Transmission type
- `fuel_type` - Fuel type
- `body_type` - Body style
- `description` - Car description
- `source_url` - Original listing URL (unique)
- `source_website` - Source website name
- `image_url` - Car image URL
- `location` - Geographic location
- `vin` - Vehicle Identification Number
- `dealer_name` - Dealer name
- `posted_date` - Original posting date
- `created_at` - Database record creation time
- `updated_at` - Database record update time

## Configuration

The application uses SQLite by default. To use a different database, update the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_car_browser
DB_USERNAME=root
DB_PASSWORD=
```

## Development

### Running Tests

```bash
php artisan test
```

### Linting

```bash
./vendor/bin/pint
```

## Notes

The current scraper implementation uses sample data for demonstration purposes. In a production environment, you would need to:

1. Implement actual web scraping logic for each source website
2. Handle rate limiting and respect robots.txt
3. Implement proper error handling and retry logic
4. Consider using a queuing system for large-scale scraping
5. Add caching mechanisms to reduce redundant scraping
6. Implement change detection to only update modified listings

## License

MIT License
