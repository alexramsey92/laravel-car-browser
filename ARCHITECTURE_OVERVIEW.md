# Updated Scraper System - Overview

## Architecture

```
┌─────────────────────────────────────────────┐
│         Web UI (/admin/scrape)              │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ Quick Start                         │   │
│  │ [▶️ Run Test Scraper]               │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ Advanced Options                    │   │
│  │ ☑ Test Scraper                     │   │
│  │ ☐ Cars.com                         │   │
│  │ ☐ Dry Run                          │   │
│  │ ☑ Save to DB                       │   │
│  │ [▶️ Run] [Select All] [Deselect]    │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ Available Scrapers Info             │   │
│  │ - Test Scraper (Ready ✅)           │   │
│  │ - Cars.com (In Dev 🔨)              │   │
│  └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
                    │
                    ▼
    ┌───────────────────────────────┐
    │    AdminController            │
    │    runScraper() method        │
    └───────────────────────────────┘
                    │
                    ▼
    ┌───────────────────────────────┐
    │    ScraperManager             │
    │ - Register scrapers           │
    │ - Run selected sources        │
    │ - Collect results             │
    │ - Generate summary            │
    └───────────────────────────────┘
                    │
                    ├─────────────────┬──────────────┐
                    ▼                 ▼              ▼
         ┌──────────────────┐  ┌──────────────┐  ┌──────────────┐
         │  BaseScraper     │  │ TestScraper  │  │CarsComScraper│
         │                  │  │              │  │              │
         │ - HTTP client    │  │ Sample data  │  │ HTML parsing │
         │ - URL fetcher    │  │ (3 cars)     │  │ CSS selectors│
         │ - Config opts    │  │              │  │              │
         └──────────────────┘  └──────────────┘  └──────────────┘
                                      │                   │
                                      └─────────┬─────────┘
                                                │
                                                ▼
                                        ┌──────────────────┐
                                        │  Database        │
                                        │  (Save results)  │
                                        └──────────────────┘
```

## Data Flow

### 1. User Selects Options in UI
```
Source: [✓] Test Scraper, [ ] Cars.com
Options: [✓] Save to DB, [ ] Dry Run
```

### 2. Form Submitted to AdminController
```php
POST /admin/scrape/run
{
  sources: ['test'],
  save_to_db: true,
  dry_run: false
}
```

### 3. ScraperManager Processes Request
```
1. Validate sources
2. Load Test Scraper
3. Run scraper.scrape()
4. Get array of cars
5. Save to database (if enabled)
6. Collect results
7. Generate summary
```

### 4. Results Displayed to User
```
Status: ✅ Success
Total Cars: 3
Scrapers Succeeded: 1
Scrapers Failed: 0

Results Table:
Source  | Status | Count
test    | ✅     | 3
```

## Component Interactions

### UI → Controller
- Form submission with selected sources and options
- Passes data via POST request

### Controller → ScraperManager
- Dependency injection
- Calls `runScrapers()` with options
- Gets summary results

### ScraperManager → Individual Scrapers
- Retrieves registered scrapers
- Calls `scrape()` method on each
- Collects results

### Scrapers → External Sources (Potential)
- Fetch HTML/JSON from websites
- Parse and extract car data
- Return standardized car array

### Scrapers → Database
- Pass car data to Car::updateOrCreate()
- Updates existing listings
- Creates new listings

## Files Changed

```
app/
├── Http/
│   └── Controllers/
│       └── AdminController.php (UPDATED)
│           ├── New: runScraper() uses ScraperManager
│           ├── New: Handles multiple sources
│           └── New: Dry run support
└── Services/
    └── Scraper/
        ├── BaseScraper.php (NEW)
        ├── ScraperManager.php (NEW)
        └── Scrapers/
            ├── TestScraper.php (NEW)
            └── CarsComScraper.php (NEW)

resources/
└── views/
    └── admin/
        └── scrape.blade.php (UPDATED)
            ├── New: Source selection checkboxes
            ├── New: Options toggles
            ├── New: Quick start button
            ├── New: Tips section
            └── New: Modern styling

docs/
├── SCRAPER_GUIDE.md (NEW)
└── SCRAPER_UI_UPDATE.md (NEW)
```

## Features Comparison

### Before
- Single "Run Scraper Now" button
- No source selection
- No dry run option
- No feedback on which sources ran
- Old sample data generator

### After
- Multiple scraper sources
- Select which sources to run
- Quick start for testing
- Dry run mode for previewing
- Detailed results summary
- Modern, user-friendly UI
- Real HTTP scraping capability
- Extensible scraper system
- Command-line control
- Detailed documentation

## Usage Examples

### Scenario 1: Quick Test
1. Click "Run Test Scraper"
2. 3 sample cars added to database
3. ✅ Done

### Scenario 2: Test New Scraper
1. Check "Cars.com" only
2. Check "Dry Run"
3. Click "Run Selected Scrapers"
4. Preview results without database changes
5. Adjust CSS selectors if needed
6. ✅ Ready for production use

### Scenario 3: Run All Scrapers
1. Click "Select All"
2. Leave "Dry Run" unchecked
3. Click "Run Selected Scrapers"
4. All scrapers run and save results
5. ✅ Database updated with all listings

### Scenario 4: Command Line
```bash
# Quick test
php artisan cars:scrape --test

# Run all
php artisan cars:scrape

# Specific source
php artisan cars:scrape --source=cars.com

# Dry run
php artisan cars:scrape --test --no-save
```

## Error Handling

### In UI
- Form validation
- Error messages displayed
- Failed scrapers shown in results
- Clear status indicators

### In Code
- Try-catch blocks in ScraperManager
- Individual scraper error handling
- Detailed logging in Laravel log
- Graceful failure (one scraper failure doesn't stop others)

## Next Development Steps

1. **Add More Scrapers**
   - Implement real Cars.com scraper
   - Add AutoTrader, CarGurus, CarMax

2. **Add Filters**
   - Filter results by price range
   - Filter by make/model
   - Filter by year

3. **Schedule Integration**
   - Display next scheduled run time
   - Allow schedule configuration
   - Show last run results

4. **Progress Tracking**
   - Real-time progress via WebSocket
   - Progress bar
   - Live log display

5. **Export/Reports**
   - Export results to CSV
   - Generate reports
   - Email notifications

6. **Performance**
   - Queue long-running scrapers
   - Background job processing
   - Caching improvements
