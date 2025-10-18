<?php

namespace App\Console\Commands;

use App\Services\Scraper\ScraperManager;
use Illuminate\Console\Command;

class ScrapeCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:scrape {--source=* : Specific source(s) to scrape (leave empty for all)} {--list : List available sources} {--test : Use test scraper} {--no-save : Don\'t save to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape cars from configured sources';

    /**
     * Execute the console command.
     */
    public function handle(ScraperManager $manager)
    {
        // List available scrapers
        if ($this->option('list')) {
            $this->listScrapers($manager);
            return Command::SUCCESS;
        }

        $this->info('🚗 Starting car scraper...');

        // Determine which scrapers to run
        $sources = $this->option('source');
        
        if ($this->option('test')) {
            $sources = ['test'];
            $this->info('Running test scraper only...');
        } elseif (empty($sources)) {
            $this->info('Running all scrapers...');
        } else {
            $this->info('Running specific scrapers: ' . implode(', ', $sources));
        }

        // Run scrapers
        $save = !$this->option('no-save');
        
        if (empty($sources)) {
            $results = $manager->runAll($save);
        } elseif ($this->option('test')) {
            $results = $manager->runScraper('test', $save);
        } else {
            $results = $manager->runScrapers($sources, $save);
        }

        // Display results
        $this->displayResults($manager);

        return Command::SUCCESS;
    }

    /**
     * List available scrapers
     */
    protected function listScrapers(ScraperManager $manager): void
    {
        $scrapers = $manager->getAvailableScrapers();
        
        $this->info("\nAvailable scrapers:");
        $this->line('');
        
        foreach ($scrapers as $scraper) {
            $this->line("  • {$scraper}");
        }
        
        $this->line('');
    }

    /**
     * Display scraping results
     */
    protected function displayResults(ScraperManager $manager): void
    {
        $summary = $manager->getSummary();

        $this->newLine();
        $this->info('📊 Scraping Summary:');
        $this->line('');

        $headers = ['Source', 'Status', 'Count'];
        $rows = [];

        foreach ($summary['results'] as $source => $result) {
            $status = $result['status'] === 'success' ? '✅ Success' : '❌ Failed';
            $count = $result['count'] ?? 0;
            
            if ($result['status'] === 'failed') {
                $count = $result['error'] ?? 'Error';
            }
            
            $rows[] = [$source, $status, $count];
        }

        $this->table($headers, $rows);

        $this->line('');
        $this->info("Total cars scraped: {$summary['total_cars']}");
        $this->info("Successful scrapers: {$summary['scrapers_succeeded']}");
        
        if ($summary['scrapers_failed'] > 0) {
            $this->warn("Failed scrapers: {$summary['scrapers_failed']}");
        }

        $this->newLine();
    }
}
