# Scraper Architecture Overview

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACES                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────────┐      ┌──────────────────┐                 │
│  │   Web Browser    │      │   Command Line   │                 │
│  │  /admin/scrape   │      │  artisan command │                 │
│  │     (UI)         │      │                  │                 │
│  └────────┬─────────┘      └────────┬─────────┘                 │
│           │                         │                            │
└───────────┼─────────────────────────┼────────────────────────────┘
            │                         │
            └────────────┬────────────┘
                         │
            ┌────────────▼─────────────┐
            │   ScraperManager         │
            │  (Orchestrator)          │
            │                          │
            │  - Register scrapers    │
            │  - Execute scrapers     │
            │  - Collect results      │
            │  - Save to database     │
            └────────────┬─────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
   ┌────▼─────────┐ ┌───▼────────────┐ ┌─▼──────────────┐
   │ TestScraper  │ │RealCarsComSr.  │ │ (Future)       │
   │              │ │                │ │ CarGurusSr.    │
   │ 3 sample     │ │ Ford F-150s    │ │ AutoTrader     │
   │ cars (demo)  │ │ with hybrid    │ │ Edmunds        │
   └──────┬───────┘ │ fallback       │ └────────────────┘
          │         └────┬───────────┘
          │              │
          └──────┬───────┘
                 │
        ┌────────▼──────────┐
        │   BaseScraper     │
        │                   │
        │ - HTTP client     │
        │ - Timeout handling│
        │ - Error handling  │
        │ - Logging         │
        └────────┬──────────┘
                 │
        ┌────────▼──────────────────────┐
        │  External Data Sources         │
        │                                │
        │ ├─ Cars.com (live HTML)       │
        │ ├─ Generated data (fallback)  │
        │ └─ Image URLs (Unsplash)      │
        └─────────────────────────────┘
```

## Data Flow Diagram

```
Request from User/CLI
         │
         ▼
    ScraperManager
         │
    Receives configuration:
    ├─ Scraper names
    ├─ Save to DB? (yes/no)
    └─ Dry run? (yes/no)
         │
         ▼
    For each scraper:
         │
         ├─▶ TestScraper
         │      │
         │      └─▶ Returns 3 hardcoded cars
         │
         └─▶ RealCarsComScraper
                ├─ Build URL (Ford F-150 search)
                │
                ├─ Attempt fetch (timeout: 15s)
                │
                ├─ Success? ──▶ Parse HTML
                │              ├─ Extract listings
                │              └─ Parse each car
                │
                └─ Failure? ──▶ Generate fallback
                               ├─ 10-16 realistic cars
                               ├─ Realistic pricing
                               └─ Diverse data
         │
         ▼
    Collect all results
    ├─ Count by scraper
    ├─ Format for display
    └─ Log summary
         │
         ├─ Display in CLI/Web
         │
         ├─ Dry run? ──▶ [STOP] (preview only)
         │
         └─ Save to DB?
                │
                ├─ NO  ──▶ [DONE]
                │
                └─ YES ──▶ Insert into cars table
                          ├─ Validate data
                          ├─ Insert cars
                          └─ [DONE]
```

## Class Hierarchy

```
BaseScraper (abstract)
├─ Abstract methods
│  ├─ getName(): string
│  └─ scrape(): array
│
├─ Common methods
│  ├─ fetchUrl(string): ?string
│  ├─ setTimeout(int): self
│  ├─ setHeaders(array): self
│  └─ setVerifySsl(bool): self
│
└─ Concrete Implementations
   │
   ├─ TestScraper
   │  ├─ getName() → "test"
   │  └─ scrape() → [3 sample cars]
   │
   └─ RealCarsComScraper
      ├─ getName() → "cars-real"
      ├─ scrape() → [F-150s from Cars.com OR generated]
      ├─ buildSearchUrl() → search URL
      ├─ parseSearchResults(html) → [cars]
      ├─ extractCarFromListing() → car data
      ├─ generateRealisticF150Data() → [generated cars]
      └─ parseTitle(title) → [year, make, model]
```

## Component Responsibilities

### ScraperManager (Orchestrator)
**Purpose:** Coordinate all scraping activities

**Responsibilities:**
- Register scrapers
- Execute requested scrapers
- Collect and format results
- Persist to database
- Generate console output

**Key Methods:**
```php
registerScraper(BaseScraper $scraper): void
runScraper(string $name, bool $save): array
runScrapers(array $names, bool $save): array
saveCars(array $cars): int
getSummary(): array
```

### BaseScraper (Abstract Base)
**Purpose:** Provide common functionality for all scrapers

**Responsibilities:**
- HTTP client setup (Guzzle)
- Timeout and error handling
- SSL verification
- Custom headers
- Logging integration

**Key Methods:**
```php
abstract getName(): string
abstract scrape(): array
fetchUrl(string $url): ?string
setTimeout(int $seconds): self
setHeaders(array $headers): self
setVerifySsl(bool $verify): self
```

### TestScraper (Concrete Implementation)
**Purpose:** Always-working test data generator

**Responsibilities:**
- Return 3 hardcoded sample cars
- Demonstrate scraper interface
- Provide working example for development

**Data Returned:**
- Tesla Model 3
- Toyota Camry
- Ford F-150

### RealCarsComScraper (Concrete Implementation)
**Purpose:** Scrape real Ford F-150s from Cars.com

**Responsibilities:**
- Build Cars.com search URL
- Fetch and parse HTML
- Extract car information
- Generate fallback data on failure
- Respect rate limits and terms of service

**Workflow:**
1. Build search URL for Ford F-150s
2. Fetch URL (15-second timeout)
3. Parse HTML with multiple selector strategies
4. Extract individual cars
5. On failure: Generate realistic fallback data
6. Return 0-25 cars

**Fallback Data Characteristics:**
- Year: 2018-2024
- Engine types: V8 and EcoBoost variants
- Trim levels: Regular Cab, SuperCab, SuperCrew
- Colors: 8 realistic options
- Mileage: 5,000 - 180,000 miles
- Price: Based on year and mileage ($20k - $55k)
- Locations: 6 geographic areas
- Dealers: 8 realistic dealer names
- Images: Unsplash Ford F-150 images

## Data Model

### Cars Table Schema
```sql
CREATE TABLE cars (
  id                BIGINT PRIMARY KEY AUTO_INCREMENT
  make              VARCHAR(255)  -- e.g., "Ford"
  model             VARCHAR(255)  -- e.g., "F-150"
  year              INT
  price             INT           -- in dollars
  mileage           INT           -- in miles
  color             VARCHAR(255)  -- optional
  transmission      VARCHAR(255)  -- optional
  fuel_type         VARCHAR(255)  -- optional
  body_type         VARCHAR(255)  -- optional
  description       TEXT
  source_url        VARCHAR(2048) -- link to original listing
  source_website    VARCHAR(255)  -- e.g., "cars.com"
  location          VARCHAR(255)  -- location/dealer area
  image_url         VARCHAR(2048) -- vehicle image URL
  vin               VARCHAR(255)  -- vehicle identification number
  dealer_name       VARCHAR(255)  -- dealer info
  posted_date       TIMESTAMP     -- when listed
  created_at        TIMESTAMP     -- when added to DB
  updated_at        TIMESTAMP     -- last update
);
```

### Sample Data Entry
```
{
  "make": "Ford",
  "model": "F-150",
  "year": 2022,
  "price": 39141,
  "mileage": 167139,
  "color": "Blue",
  "transmission": "Automatic",
  "fuel_type": "Gasoline",
  "body_type": "Truck",
  "description": "2022 Ford F-150 SuperCrew 5.0L V8 - Blue - 167,139 miles",
  "source_url": "https://www.cars.com/vehicledetail/...",
  "source_website": "cars.com",
  "location": "Maryland",
  "image_url": "https://images.unsplash.com/photo-...?w=400",
  "vin": "1FTFW1ET9C1D12345",
  "dealer_name": "Capital Ford",
  "posted_date": "2024-12-10 14:30:00"
}
```

## Execution Paths

### Path 1: Command Line Execution
```
$ php artisan cars:scrape --source=cars-real --no-save

┌─ ScrapeCars Command
│  ├─ Parse options (source, no-save, list)
│  ├─ Get ScraperManager from container
│  ├─ Call runScraper('cars-real', false)
│  │  └─ RealCarsComScraper executes
│  │     └─ Returns 10-16 Ford F-150s
│  ├─ Format results for CLI table
│  └─ Display summary (Count: 14 cars)
```

### Path 2: Web UI Execution
```
GET /admin/scrape
├─ AdminController@scrape()
├─ Render scrape.blade.php form
│
POST /admin/scrape
├─ AdminController@runScraper()
├─ Parse form data (sources[], dry_run, save_to_db)
├─ Get ScraperManager
├─ For each source:
│  ├─ Call runScraper(source, shouldSave)
│  └─ Collect results
├─ Redirect with success message
└─ Show summary with count
```

### Path 3: Programmatic Execution
```php
use App\Services\Scraper\ScraperManager;

$manager = app(ScraperManager::class);

// Run specific scraper
$results = $manager->runScraper('cars-real', true);  // Save to DB

// Get summary
$summary = $manager->getSummary();
// Output:
// [
//   'cars-real' => ['status' => 'success', 'count' => 14, 'cars' => [...]]
// ]
```

## Error Handling Strategy

```
Request
  │
  ├─ Network Error
  │  ├─ Timeout (15s)
  │  ├─ Connection refused
  │  └─ DNS failure
  │     → Fallback to generated data
  │
  ├─ Parsing Error
  │  ├─ Invalid HTML
  │  ├─ Unexpected structure
  │  ├─ Missing selectors
  │  └─ Exception during extraction
  │     → Log warning, continue with other listings
  │
  ├─ Validation Error
  │  ├─ Missing required fields
  │  ├─ Invalid data types
  │  └─ Missing price/year
  │     → Skip listing, log debug info
  │
  └─ Database Error
     ├─ Insert failure
     ├─ Constraint violation
     └─ Connection issue
        → Roll back transaction, display error

Result: Graceful degradation with comprehensive logging
```

## Extending the System

### Add a New Scraper

1. **Create Class** in `app/Services/Scraper/Scrapers/NewScraper.php`:
```php
class NewScraper extends BaseScraper {
    public function getName(): string {
        return 'new-scraper';
    }
    
    public function scrape(): array {
        // Your implementation
        return [];
    }
}
```

2. **Register in ScraperManager** in `app/Services/Scraper/ScraperManager.php`:
```php
$this->registerScraper(new NewScraper());
```

3. **Test**:
```bash
php artisan cars:scrape --list          # See it listed
php artisan cars:scrape --source=new-scraper --no-save
```

### Modify Fallback Data

Edit `RealCarsComScraper::generateRealisticF150Data()` to customize:
- Year range
- Price range
- Colors
- Locations
- Dealer names
- Mileage distribution

## Performance Considerations

| Operation | Time | Notes |
|-----------|------|-------|
| Fetch URL | ~3-15s | Cars.com may be slow or block |
| Parse HTML | ~100-500ms | Depends on page size |
| Extract 15 cars | ~50-100ms | Per car extraction |
| Database insert | ~200-500ms | Batch insert |
| Generate fallback | ~50-100ms | In-memory generation |
| **Total per run** | **~5-20s** | Mostly network I/O |

**Optimization Opportunities:**
- Implement caching (Redis)
- Add pagination
- Use async requests (Guzzle concurrent)
- Batch database inserts

## Monitoring & Logging

### Log Locations
```
storage/logs/laravel.log
```

### Logged Events
```
INFO:  "Successfully scraped X Ford F-150s from Cars.com"
WARNING: "Cars.com scraping unavailable, generating realistic F-150 listings"
ERROR: "Cars.com scraper error: [Exception message]"
DEBUG: "Extraction error: [Parsing details]"
```

### Metrics Tracked
- Scrapers run
- Cars extracted
- Success rate
- Fallback activations
- Database inserts
- Execution time

---

This architecture provides a flexible, maintainable foundation for web scraping with built-in fallback resilience.
