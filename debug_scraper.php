<?php

// Quick debug script to check Cars.com structure
require 'vendor/autoload.php';

$url = 'https://www.cars.com/shopping/results/?stock_type=all&makes%5B%5D=ford&models%5B%5D=ford-f_150&maximum_distance=all&zip=21769';

echo "Fetching: $url\n\n";

$client = new \GuzzleHttp\Client([
    'timeout' => 10,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    ],
]);

try {
    $response = $client->get($url);
    $html = $response->getBody()->getContents();
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->getHeader('Content-Type')[0] . "\n";
    echo "HTML Length: " . strlen($html) . " bytes\n\n";
    
    // Look for common patterns
    echo "=== Searching for patterns ===\n";
    
    // Check for articles
    if (preg_match_all('/<article/i', $html, $matches)) {
        echo "Found " . count($matches[0]) . " <article> tags\n";
    }
    
    // Check for divs with class
    if (preg_match_all('/<div[^>]*class="[^"]*listing[^"]*"[^>]*>/i', $html, $matches)) {
        echo "Found " . count($matches[0]) . " divs with 'listing' class\n";
    }
    
    // Check for price patterns
    if (preg_match_all('/\$[\d,]+/i', $html, $matches)) {
        echo "Found " . count($matches[0]) . " price patterns\n";
        echo "First 5 prices: " . implode(', ', array_slice($matches[0], 0, 5)) . "\n";
    }
    
    // Check for vehicle models
    if (preg_match_all('/(ford|f-150|f150)/i', $html, $matches)) {
        echo "Found " . count($matches[0]) . " Ford F-150 references\n";
    }
    
    // Save first 10KB to inspect
    echo "\n=== First 3000 characters ===\n";
    echo substr($html, 0, 3000) . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
