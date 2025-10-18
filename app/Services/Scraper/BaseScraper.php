<?php

namespace App\Services\Scraper;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Base scraper class for common scraping functionality
 */
abstract class BaseScraper
{
    protected Client $httpClient;
    protected array $headers = [];
    protected int $timeout = 10;
    protected bool $verifySsl = true;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => $this->timeout,
            'verify' => $this->verifySsl,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]);
    }

    /**
     * Fetch HTML content from a URL
     */
    protected function fetchUrl(string $url): ?string
    {
        try {
            Log::info("Fetching URL: {$url}");
            $response = $this->httpClient->get($url);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error("Failed to fetch {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Set custom headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set timeout
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Set SSL verification
     */
    public function setVerifySsl(bool $verify): self
    {
        $this->verifySsl = $verify;
        return $this;
    }

    /**
     * Abstract method to scrape cars
     */
    abstract public function scrape(): array;

    /**
     * Get scraper name
     */
    abstract public function getName(): string;
}
