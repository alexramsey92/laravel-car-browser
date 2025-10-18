# Car Scraper System

This document explains how to use and extend the car scraper system.

## Quick Start

### Run Test Scraper (Recommended for testing)
```bash
php artisan cars:scrape --test
```

This will scrape test data and save it to the database.

### Run All Scrapers
```bash
php artisan cars:scrape
```

### Run Specific Scraper
```bash
php artisan cars:scrape --source=cars.com
```

### Run Multiple Specific Scrapers
```bash
php artisan cars:scrape --source=cars.com --source=test
```

### List Available Scrapers
```bash
php artisan cars:scrape --list
```

### Dry Run (Don't save to database)
```bash
php artisan cars:scrape --no-save
```

## Available Scrapers

### 1. Test Scraper (`test`)
- **Status**: Ready to use
- **Purpose**: Development and testing
- **Returns**: 3 sample cars (Tesla Model 3, Toyota Camry, Ford F-150)
- **Usage**: `php artisan cars:scrape --test`

### 2. Cars.com Scraper (`cars.com`)
- **Status**: Template/Example
- **Purpose**: Scrape cars.com listings
- **Note**: Uses example CSS selectors - needs to be adjusted to match current site structure
- **Usage**: `php artisan cars:scrape --source=cars.com`

## Architecture

### File Structure
```
app/Services/Scraper/
├── BaseScraper.php          # Abstract base class for all scrapers
├── ScraperManager.php       # Manages and coordinates scrapers
└── Scrapers/
    ├── TestScraper.php      # Test scraper (always functional)
    └── CarsComScraper.php   # Example real scraper
```

### Key Classes

#### BaseScraper
Abstract base class that all scrapers extend. Provides:
- HTTP client with proper headers and timeouts
- URL fetching with error handling
- Configuration methods (setTimeout, setHeaders, etc.)

#### ScraperManager
Coordinates all scrapers:
- Registers scrapers
- Runs individual or multiple scrapers
- Saves results to database
- Provides summary information

#### Individual Scrapers
Each scraper implements:
- `getName()`: Returns scraper identifier
- `scrape()`: Fetches and parses car data
- Returns array of car data

## Creating a New Scraper

### Step 1: Create Scraper Class
Create a new file in `app/Services/Scraper/Scrapers/` that extends `BaseScraper`:

```php
<?php

namespace App\Services\Scraper\Scrapers;

use App\Services\Scraper\BaseScraper;

class MyCustomScraper extends BaseScraper
{
    protected string $baseUrl = 'https://www.mysite.com';

    public function getName(): string
    {
        return 'mysite';
    }

    public function scrape(): array
    {
        // Fetch and parse cars
        $html = $this->fetchUrl($this->baseUrl . '/listings');
        
        // Parse HTML and extract car data
        $cars = $this->parseListings($html);
        
        return $cars;
    }

    protected function parseListings(string $html): array
    {
        // Your parsing logic here
        return [];
    }
}
```

### Step 2: Register Scraper
Edit `app/Services/Scraper/ScraperManager.php` and add to `__construct()`:

```php
$this->registerScraper(new MyCustomScraper());
```

### Step 3: Use Your Scraper
```bash
php artisan cars:scrape --source=mysite
```

## Parsing HTML

The scrapers use PHP's DOMDocument and DOMXPath for HTML parsing. Example:

```php
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Find elements by CSS class
$listings = $xpath->query('//div[@class="car-listing"]');

// Find by ID
$element = $xpath->query('//span[@id="price"]');

// Find by multiple conditions
$elements = $xpath->query('//a[@class="link" and @data-type="car"]');
```

## Data Format

Each scraper must return an array of cars with this structure:

```php
[
    'make' => 'Toyota',
    'model' => 'Camry',
    'year' => 2023,
    'price' => 28500,
    'mileage' => 15000,
    'color' => 'Blue',
    'transmission' => 'Automatic',
    'fuel_type' => 'Gasoline',
    'body_type' => 'Sedan',
    'description' => 'Well-maintained Camry',
    'source_url' => 'https://example.com/listing/123',
    'source_website' => 'cars.com',
    'image_url' => 'https://example.com/image.jpg',
    'location' => 'New York, NY',
    'vin' => 'JTNC111E3X1078401',
    'dealer_name' => 'Local Auto Sales',
    'posted_date' => now(),
]
```

## Tips for Real Scrapers

### Handling Dynamic Content
If the site uses JavaScript to load listings, you may need Puppeteer or Playwright:
- [PHP Puppeteer](https://github.com/nesk/puphpeteer)
- Use in your scraper to render JavaScript before parsing

### Respecting Rate Limits
Add delays between requests:
```php
sleep(2); // Wait 2 seconds between requests
usleep(500000); // Or 0.5 seconds
```

### Handling Pagination
Loop through pages:
```php
for ($page = 1; $page <= 10; $page++) {
    $url = $this->baseUrl . '/listings?page=' . $page;
    $html = $this->fetchUrl($url);
    // Parse and collect results
}
```

### Error Handling
Always wrap parsing in try-catch:
```php
try {
    // Parse logic
} catch (\Exception $e) {
    Log::error("Parse error: " . $e->getMessage());
}
```

## Troubleshooting

### "Scraper not found" Error
Make sure scraper is registered in `ScraperManager::__construct()`

### SSL Certificate Errors
Disable SSL verification (use cautiously):
```php
$scraper->setVerifySsl(false);
```

### Timeout Issues
Increase timeout:
```php
$scraper->setTimeout(30); // 30 seconds
```

### HTML Parsing Issues
1. Inspect the website HTML structure
2. Update CSS selectors in your scraper
3. Use browser DevTools to find correct selectors
4. Test with `--no-save` first

## Testing Your Scraper

1. Run with `--no-save` to test without saving:
```bash
php artisan cars:scrape --source=mysite --no-save
```

2. Check logs for errors:
```bash
tail -f storage/logs/laravel.log
```

3. Verify data structure matches expected format

4. Test with a small subset of data first
