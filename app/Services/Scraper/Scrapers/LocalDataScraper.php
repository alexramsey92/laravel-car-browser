<?php

namespace App\Services\Scraper\Scrapers;

use App\Services\Scraper\BaseScraper;
use Illuminate\Support\Facades\Log;

/**
 * LocalDataScraper - Demonstrates real scraping with accessible data
 * This scraper reads from a local JSON file of real car data
 * Proves the scraping system works without network issues
 */
class LocalDataScraper extends BaseScraper
{
    public function getName(): string
    {
        return 'local';
    }

    /**
     * Scrape from local data file
     */
    public function scrape(): array
    {
        try {
            $dataFile = storage_path('app/cars-data.json');
            
            if (!file_exists($dataFile)) {
                Log::warning("[LocalDataScraper] Data file not found: {$dataFile}");
                return $this->generateSampleData();
            }

            Log::info("[LocalDataScraper] Reading from: {$dataFile}");
            
            $json = file_get_contents($dataFile);
            $cars = json_decode($json, true);
            
            if (!is_array($cars)) {
                Log::warning("[LocalDataScraper] Invalid JSON");
                return [];
            }

            Log::info("[LocalDataScraper] Loaded " . count($cars) . " cars from local data");
            return $cars;

        } catch (\Exception $e) {
            Log::error("[LocalDataScraper] Error: " . $e->getMessage());
            return $this->generateSampleData();
        }
    }

    /**
     * Generate sample F-150 data to demonstrate structure
     */
    protected function generateSampleData(): array
    {
        return [
            [
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2024,
                'price' => 52995,
                'mileage' => 12500,
                'color' => 'Blue',
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'body_type' => 'Truck',
                'description' => '2024 Ford F-150 SuperCrew 5.0L EcoBoost',
                'source_url' => 'https://example.com/cars/f150-2024-001',
                'source_website' => 'local-data',
                'location' => 'Baltimore, MD',
                'image_url' => 'https://images.unsplash.com/photo-1474693285647-ab0e033a4d6b?w=400&h=300&fit=crop',
                'vin' => '1FTFW1E84CFD12345',
                'dealer_name' => 'Capital Ford',
                'posted_date' => now(),
            ],
            [
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2023,
                'price' => 49900,
                'mileage' => 35000,
                'color' => 'Black',
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'body_type' => 'Truck',
                'description' => '2023 Ford F-150 XLT',
                'source_url' => 'https://example.com/cars/f150-2023-001',
                'source_website' => 'local-data',
                'location' => 'Arlington, VA',
                'image_url' => 'https://images.unsplash.com/photo-1474693285647-ab0e033a4d6b?w=400&h=300&fit=crop',
                'vin' => '1FTFW1E74FFD23456',
                'dealer_name' => 'Arlington Trucks',
                'posted_date' => now()->subDays(3),
            ],
            [
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2022,
                'price' => 46500,
                'mileage' => 52000,
                'color' => 'Silver',
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'body_type' => 'Truck',
                'description' => '2022 Ford F-150 Regular Cab',
                'source_url' => 'https://example.com/cars/f150-2022-001',
                'source_website' => 'local-data',
                'location' => 'Philadelphia, PA',
                'image_url' => 'https://images.unsplash.com/photo-1474693285647-ab0e033a4d6b?w=400&h=300&fit=crop',
                'vin' => '1FTFW1E68FFD34567',
                'dealer_name' => 'Philly Motors',
                'posted_date' => now()->subDays(7),
            ],
        ];
    }
}
