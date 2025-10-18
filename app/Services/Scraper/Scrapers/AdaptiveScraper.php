<?php

namespace App\Services\Scraper\Scrapers;

use App\Services\Scraper\BaseScraper;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced scraper with multiple fallback strategies
 * Works with various car listing websites
 */
class AdaptiveScraper extends BaseScraper
{
    protected string $baseUrl;
    protected int $maxPages = 1;
    protected int $delayMs = 2000;

    public function __construct(string $baseUrl = 'https://www.cars.com')
    {
        parent::__construct();
        $this->baseUrl = $baseUrl;
    }

    public function getName(): string
    {
        return 'adaptive';
    }

    /**
     * Scrape with multiple strategies
     */
    public function scrape(): array
    {
        $cars = [];
        
        try {
            Log::info("Starting adaptive scraper for: {$this->baseUrl}");
            
            // Try multiple URLs and parsing strategies
            $urls = $this->getSearchUrls();
            
            foreach ($urls as $searchUrl) {
                Log::info("Trying: {$searchUrl}");
                $html = $this->fetchUrl($searchUrl);
                
                if (!$html) {
                    continue;
                }

                // Try multiple parsing strategies
                $pageCars = $this->parseWithMultipleStrategies($html);
                
                if (count($pageCars) > 0) {
                    $cars = array_merge($cars, $pageCars);
                    Log::info("Found " . count($pageCars) . " cars using adaptive parser");
                    break; // Success, stop trying other URLs
                }
                
                usleep($this->delayMs * 1000);
            }
        } catch (\Exception $e) {
            Log::error("Adaptive scraper error: " . $e->getMessage());
        }
        
        Log::info("Total cars found: " . count($cars));
        return array_slice($cars, 0, 20); // Limit to 20 cars
    }

    /**
     * Get multiple search URLs to try
     */
    protected function getSearchUrls(): array
    {
        return [
            $this->baseUrl . '/for-sale/?searchSource=home_homepage',
            $this->baseUrl . '/for-sale/?pageNumber=1',
            $this->baseUrl . '/search/used-cars',
        ];
    }

    /**
     * Try multiple parsing strategies
     */
    protected function parseWithMultipleStrategies(string $html): array
    {
        $strategies = [
            'parseArticles',
            'parseInventoryListings',
            'parseVehicleCards',
            'parseGenericListings',
        ];

        foreach ($strategies as $strategy) {
            $cars = $this->$strategy($html);
            if (count($cars) > 0) {
                Log::info("Successfully parsed using strategy: {$strategy}");
                return $cars;
            }
        }

        return [];
    }

    /**
     * Strategy 1: Parse article elements
     */
    protected function parseArticles(string $html): array
    {
        $cars = [];
        
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $listings = $xpath->query('//article');
            
            foreach ($listings as $listing) {
                $car = $this->extractData($xpath, $listing);
                if ($car) {
                    $cars[] = $car;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Article parsing failed: " . $e->getMessage());
        }

        return $cars;
    }

    /**
     * Strategy 2: Parse inventory listings
     */
    protected function parseInventoryListings(string $html): array
    {
        $cars = [];
        
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $listings = $xpath->query('//*[contains(@class, "inventory") or contains(@class, "listing")]');
            
            foreach ($listings as $listing) {
                $car = $this->extractData($xpath, $listing);
                if ($car) {
                    $cars[] = $car;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Inventory listing parsing failed: " . $e->getMessage());
        }

        return $cars;
    }

    /**
     * Strategy 3: Parse vehicle cards
     */
    protected function parseVehicleCards(string $html): array
    {
        $cars = [];
        
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $listings = $xpath->query('//*[contains(@class, "vehicle") or contains(@class, "card")]');
            
            foreach ($listings as $listing) {
                $car = $this->extractData($xpath, $listing);
                if ($car) {
                    $cars[] = $car;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Vehicle card parsing failed: " . $e->getMessage());
        }

        return $cars;
    }

    /**
     * Strategy 4: Parse generic listings
     */
    protected function parseGenericListings(string $html): array
    {
        $cars = [];
        
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            // Look for anything with links and prices
            $listings = $xpath->query('//div[.//a and .//span[contains(text(), "$")]]');
            
            foreach ($listings as $listing) {
                $car = $this->extractData($xpath, $listing);
                if ($car) {
                    $cars[] = $car;
                }
            }
        } catch (\Exception $e) {
            Log::debug("Generic listing parsing failed: " . $e->getMessage());
        }

        return $cars;
    }

    /**
     * Extract car data using flexible XPath
     */
    protected function extractData(DOMXPath $xpath, $listing): ?array
    {
        try {
            // Extract title from first heading or link
            $titleNodes = $xpath->query('.//h2 | .//h3 | .//h4 | .//a[@title]', $listing);
            $title = null;
            
            foreach ($titleNodes as $node) {
                $text = trim($node->textContent);
                if (strlen($text) > 5 && strlen($text) < 100) {
                    $title = $text;
                    break;
                }
            }

            if (!$title) {
                return null;
            }

            // Extract price - look for $ followed by numbers
            $priceNodes = $xpath->query('.//*[contains(text(), "$")]', $listing);
            $price = null;
            
            foreach ($priceNodes as $node) {
                $priceText = preg_replace('/[^0-9]/', '', $node->textContent);
                if (strlen($priceText) > 3 && strlen($priceText) < 8) {
                    $price = (int)$priceText;
                    break;
                }
            }

            // Extract mileage
            $mileageNodes = $xpath->query('.//*[contains(text(), "mi") or contains(text(), "mile")]', $listing);
            $mileage = null;
            
            foreach ($mileageNodes as $node) {
                $mileageText = preg_replace('/[^0-9]/', '', $node->textContent);
                if (strlen($mileageText) > 0 && strlen($mileageText) < 7) {
                    $mileage = (int)$mileageText;
                    break;
                }
            }

            // Extract URL
            $linkNode = $xpath->query('.//a[1]', $listing)->item(0);
            if (!$linkNode || !$linkNode->hasAttribute('href')) {
                return null;
            }

            $url = $linkNode->getAttribute('href');
            if (strpos($url, 'http') !== 0) {
                $url = $this->baseUrl . $url;
            }

            // Extract image
            $imageNode = $xpath->query('.//img[1]', $listing)->item(0);
            $imageUrl = null;
            
            if ($imageNode) {
                if ($imageNode->hasAttribute('src')) {
                    $imageUrl = $imageNode->getAttribute('src');
                } elseif ($imageNode->hasAttribute('data-src')) {
                    $imageUrl = $imageNode->getAttribute('data-src');
                }
            }

            // Parse title
            [$make, $model, $year] = $this->parseTitle($title);

            // Require minimum data
            if (!$make || !$model || !$year) {
                return null;
            }

            return [
                'make' => $make,
                'model' => $model,
                'year' => $year,
                'price' => $price,
                'mileage' => $mileage,
                'description' => $title,
                'source_url' => $url,
                'source_website' => 'cars.com',
                'location' => 'Unknown',
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
            Log::debug("Data extraction error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse title to extract make, model, year
     */
    protected function parseTitle(string $title): array
    {
        $year = null;
        $make = 'Unknown';
        $model = 'Unknown';

        // Extract year
        if (preg_match('/(\d{4})/', $title, $matches)) {
            $year = (int)$matches[1];
        }

        // Split into parts
        $parts = array_filter(preg_split('/[\s\-\.]+/', trim($title)));
        
        // Remove year if present
        $parts = array_values(array_filter($parts, fn($p) => $p != $year));
        
        // Remove common words
        $commonWords = ['used', 'new', 'certified', 'pre-owned', 'sedan', 'suv', 'truck', 'for', 'sale'];
        $parts = array_filter($parts, fn($p) => !in_array(strtolower($p), $commonWords));
        $parts = array_values($parts);

        if (isset($parts[0])) {
            $make = $parts[0];
        }
        if (isset($parts[1])) {
            $model = $parts[1];
        }

        return [$make, $model, $year];
    }

    /**
     * Set max pages
     */
    public function setMaxPages(int $pages): self
    {
        $this->maxPages = $pages;
        return $this;
    }

    /**
     * Set delay between requests
     */
    public function setDelay(int $ms): self
    {
        $this->delayMs = $ms;
        return $this;
    }
}
