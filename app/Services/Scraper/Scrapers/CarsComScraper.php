<?php

namespace App\Services\Scraper\Scrapers;

use App\Services\Scraper\BaseScraper;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

/**
 * Real scraper for Cars.com listings
 * Fetches actual data from Cars.com search results
 */
class CarsComScraper extends BaseScraper
{
    protected string $baseUrl = 'https://www.cars.com';
    protected int $maxPages = 2;
    protected int $delayMs = 2000; // 2 second delay between requests

    public function getName(): string
    {
        return 'cars.com';
    }

    /**
     * Scrape cars from Cars.com
     */
    public function scrape(): array
    {
        $cars = [];
        
        try {
            // Scrape multiple pages
            for ($page = 1; $page <= $this->maxPages; $page++) {
                Log::info("Scraping Cars.com page {$page}");
                
                $searchUrl = $this->buildSearchUrl($page);
                $html = $this->fetchUrl($searchUrl);
                
                if (!$html) {
                    Log::warning("Failed to fetch page {$page}");
                    continue;
                }

                $pagesCars = $this->parseListings($html);
                $cars = array_merge($cars, $pagesCars);
                
                Log::info("Found " . count($pagesCars) . " cars on page {$page}");
                
                // Rate limiting - be respectful to the server
                if ($page < $this->maxPages) {
                    usleep($this->delayMs * 1000);
                }
            }
        } catch (\Exception $e) {
            Log::error("Cars.com scraper error: " . $e->getMessage());
        }
        
        Log::info("Total cars found on Cars.com: " . count($cars));
        return $cars;
    }

    /**
     * Build search URL
     */
    protected function buildSearchUrl(int $page): string
    {
        // Search for all used cars, sorted by newest
        $params = [
            'searchSource' => 'home_homepage',
            'pageNumber' => $page,
            'perPage' => 20,
        ];
        
        return $this->baseUrl . '/for-sale?' . http_build_query($params);
    }

    /**
     * Parse listings from HTML
     */
    protected function parseListings(string $html): array
    {
        $cars = [];
        
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            // Find car listing articles - Cars.com uses article tags
            $listings = $xpath->query('//article[contains(@class, "inventory-listing")]');
            
            if ($listings->length === 0) {
                // Try alternative selectors
                $listings = $xpath->query('//div[contains(@class, "vehicle-card")]');
            }

            foreach ($listings as $listing) {
                $car = $this->extractCarData($xpath, $listing);
                if ($car && !empty($car['source_url'])) {
                    $cars[] = $car;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error parsing listings: " . $e->getMessage());
        }

        return $cars;
    }

    /**
     * Extract car data from a listing element
     */
    protected function extractCarData(DOMXPath $xpath, $listing): ?array
    {
        try {
            // Extract title/heading
            $titleNode = $xpath->query('.//h2 | .//h3 | .//a[contains(@class, "title")] | .//span[contains(@class, "title")]', $listing)->item(0);
            $title = $titleNode ? trim($titleNode->textContent) : null;
            
            if (!$title) {
                return null;
            }

            // Extract price
            $priceNode = $xpath->query('.//*[contains(@class, "price")] | .//span[contains(@class, "primary-price")]', $listing)->item(0);
            $price = null;
            if ($priceNode) {
                $priceText = preg_replace('/[^0-9]/', '', $priceNode->textContent);
                $price = (int)$priceText ?: null;
            }

            // Extract mileage
            $mileageNode = $xpath->query('.//*[contains(text(), "mi") or contains(text(), "mile")] | .//span[contains(@class, "mileage")]', $listing)->item(0);
            $mileage = null;
            if ($mileageNode) {
                $mileageText = preg_replace('/[^0-9]/', '', $mileageNode->textContent);
                $mileage = (int)$mileageText ?: null;
            }

            // Extract listing URL
            $linkNode = $xpath->query('.//a[contains(@class, "title-link")] | .//a[@class="inventory-listing-link"] | .//a[1]', $listing)->item(0);
            $url = null;
            if ($linkNode && $linkNode->hasAttribute('href')) {
                $url = $linkNode->getAttribute('href');
                if (strpos($url, 'http') !== 0) {
                    $url = $this->baseUrl . $url;
                }
            }

            if (!$url) {
                return null;
            }

            // Extract image
            $imageNode = $xpath->query('.//img | .//img[@class="vehicle-image"]', $listing)->item(0);
            $imageUrl = null;
            if ($imageNode && $imageNode->hasAttribute('src')) {
                $imageUrl = $imageNode->getAttribute('src');
                if (!$imageUrl && $imageNode->hasAttribute('data-src')) {
                    $imageUrl = $imageNode->getAttribute('data-src');
                }
            }

            // Parse title to extract make, model, year
            [$make, $model, $year] = $this->parseTitle($title);

            // Extract location if available
            $locationNode = $xpath->query('.//*[contains(@class, "location")] | .//span[contains(@class, "address")]', $listing)->item(0);
            $location = $locationNode ? trim($locationNode->textContent) : 'Unknown';

            return [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'price' => $price,
                'mileage' => $mileage,
                'description' => $title,
                'source_url' => $url,
                'source_website' => $this->getName(),
                'location' => $location,
                'color' => null,
                'transmission' => null,
                'fuel_type' => null,
                'body_type' => null,
                'image_url' => $imageUrl,
                'vin' => null,
                'dealer_name' => null,
                'posted_date' => now(),
            ];
        } catch (\Exception $e) {
            Log::error("Error extracting car data: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse make, model, and year from title
     * Example: "2024 Honda Accord" -> ['Honda', 'Accord', 2024]
     */
    protected function parseTitle(string $title): array
    {
        // Try to extract year first
        $year = null;
        if (preg_match('/(\d{4})/', $title, $matches)) {
            $year = (int)$matches[1];
        }

        // Split on year to get make and model
        $parts = preg_split('/\s+/', trim($title));
        
        // Remove year if it's the first part
        if ($parts[0] == $year) {
            array_shift($parts);
        }

        $make = $parts[0] ?? 'Unknown';
        $model = isset($parts[1]) ? $parts[1] : 'Unknown';

        return [$make, $model, $year];
    }

    /**
     * Set max pages to scrape
     */
    public function setMaxPages(int $pages): self
    {
        $this->maxPages = $pages;
        return $this;
    }

    /**
     * Set delay between requests (in milliseconds)
     */
    public function setDelay(int $ms): self
    {
        $this->delayMs = $ms;
        return $this;
    }
}
