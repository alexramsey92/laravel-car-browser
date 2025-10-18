<?php

namespace App\Services\Scraper;

use App\Models\Car;
use App\Services\Scraper\Scrapers\CarsComScraper;
use App\Services\Scraper\Scrapers\TestScraper;
use App\Services\Scraper\Scrapers\AdaptiveScraper;
use App\Services\Scraper\Scrapers\ProductionScraper;
use App\Services\Scraper\Scrapers\RealCarsComScraper;
use App\Services\Scraper\Scrapers\LocalDataScraper;
use Illuminate\Support\Facades\Log;

/**
 * Scraper manager to control which scrapers run and aggregate results
 */
class ScraperManager
{
    protected array $scrapers = [];
    protected array $results = [];

    public function __construct()
    {
        // Register available scrapers
        $this->registerScraper(new TestScraper());
        $this->registerScraper(new LocalDataScraper());
        $this->registerScraper(new RealCarsComScraper());
    }

    /**
     * Register a scraper
     */
    public function registerScraper(BaseScraper $scraper): self
    {
        $this->scrapers[$scraper->getName()] = $scraper;
        return $this;
    }

    /**
     * Get all available scrapers
     */
    public function getAvailableScrapers(): array
    {
        return array_keys($this->scrapers);
    }

    /**
     * Run a specific scraper
     */
    public function runScraper(string $name, bool $save = true): array
    {
        if (!isset($this->scrapers[$name])) {
            Log::warning("Scraper '{$name}' not found");
            return [];
        }

        try {
            Log::info("Running scraper: {$name}");
            $scraper = $this->scrapers[$name];
            $cars = $scraper->scrape();

            if ($save) {
                $this->saveCars($cars);
            }

            $this->results[$name] = [
                'status' => 'success',
                'count' => count($cars),
                'cars' => $cars,
            ];

            Log::info("Scraper {$name} completed. Found " . count($cars) . " cars");
        } catch (\Exception $e) {
            Log::error("Scraper {$name} failed: " . $e->getMessage());
            $this->results[$name] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'count' => 0,
            ];
        }

        return $this->results[$name];
    }

    /**
     * Run all scrapers
     */
    public function runAll(bool $save = true): array
    {
        $this->results = [];

        foreach ($this->getAvailableScrapers() as $scraperName) {
            $this->runScraper($scraperName, $save);
        }

        return $this->results;
    }

    /**
     * Run specific scrapers
     */
    public function runScrapers(array $names, bool $save = true): array
    {
        $this->results = [];

        foreach ($names as $name) {
            $this->runScraper($name, $save);
        }

        return $this->results;
    }

    /**
     * Save cars to database
     */
    protected function saveCars(array $cars): int
    {
        $count = 0;

        foreach ($cars as $carData) {
            try {
                Car::updateOrCreate(
                    ['source_url' => $carData['source_url']],
                    $carData
                );
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to save car: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Get last results
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Get summary of results
     */
    public function getSummary(): array
    {
        $total = 0;
        $succeeded = 0;
        $failed = 0;

        foreach ($this->results as $result) {
            if ($result['status'] === 'success') {
                $succeeded++;
                $total += $result['count'];
            } else {
                $failed++;
            }
        }

        return [
            'total_cars' => $total,
            'scrapers_succeeded' => $succeeded,
            'scrapers_failed' => $failed,
            'results' => $this->results,
        ];
    }
}
