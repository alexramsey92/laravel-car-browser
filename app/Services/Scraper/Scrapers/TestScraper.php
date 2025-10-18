<?php

namespace App\Services\Scraper\Scrapers;

use App\Services\Scraper\BaseScraper;

/**
 * Test scraper for development and testing purposes
 * Returns mock data but uses the same structure as real scrapers
 */
class TestScraper extends BaseScraper
{
    public function getName(): string
    {
        return 'test';
    }

    /**
     * Return test data for development
     */
    public function scrape(): array
    {
        return [
            [
                'make' => 'Tesla',
                'model' => 'Model 3',
                'year' => 2024,
                'price' => 42990,
                'mileage' => 0,
                'color' => 'White',
                'transmission' => 'Automatic',
                'fuel_type' => 'Electric',
                'body_type' => 'Sedan',
                'description' => '2024 Tesla Model 3 - Premium Electric Sedan',
                'source_url' => 'https://test.com/listing/tesla-model-3-2024',
                'source_website' => $this->getName(),
                'image_url' => 'https://images.unsplash.com/photo-1511884642898-4c92249e20b6?w=400&h=300&fit=crop',
                'location' => 'New York, NY',
                'vin' => '5YJ3E7EA3L00001',
                'dealer_name' => 'Test Motors',
                'posted_date' => now(),
            ],
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
                'description' => '2023 Toyota Camry - Reliable Family Sedan',
                'source_url' => 'https://test.com/listing/toyota-camry-2023',
                'source_website' => $this->getName(),
                'image_url' => 'https://images.unsplash.com/photo-1552820728-8ac54d571e27?w=400&h=300&fit=crop',
                'location' => 'Los Angeles, CA',
                'vin' => 'JTNC111E3X1078401',
                'dealer_name' => 'Test Motors',
                'posted_date' => now()->subDays(5),
            ],
            [
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2024,
                'price' => 55000,
                'mileage' => 2000,
                'color' => 'Black',
                'transmission' => 'Automatic',
                'fuel_type' => 'Gasoline',
                'body_type' => 'Truck',
                'description' => '2024 Ford F-150 - Powerful Pickup Truck',
                'source_url' => 'https://test.com/listing/ford-f150-2024',
                'source_website' => $this->getName(),
                'image_url' => 'https://images.unsplash.com/photo-1474693285647-ab0e033a4d6b?w=400&h=300&fit=crop',
                'location' => 'Dallas, TX',
                'vin' => '1FTFW1ET4DFE12345',
                'dealer_name' => 'Test Motors',
                'posted_date' => now()->subDays(1),
            ],
        ];
    }
}
