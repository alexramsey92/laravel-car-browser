<?php

namespace App\Console\Commands;

use App\Services\CarScraperService;
use Illuminate\Console\Command;

class ScrapeCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape cars from all configured sources';

    /**
     * Execute the console command.
     */
    public function handle(CarScraperService $scraper)
    {
        $this->info('Starting car scraper...');
        
        $count = $scraper->scrapeAll();
        
        $this->info("Successfully scraped {$count} cars from all sources.");
        
        return Command::SUCCESS;
    }
}
