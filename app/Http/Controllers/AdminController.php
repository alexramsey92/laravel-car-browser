<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Services\Scraper\ScraperManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_cars' => Car::count(),
            'total_makes' => Car::distinct('make')->count('make'),
            'avg_price' => Car::avg('price'),
            'latest_cars' => Car::latest()->take(5)->get(),
        ];

        return view('admin.index', compact('stats'));
    }

    public function cars(Request $request)
    {
        $query = Car::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $cars = $query->latest()->paginate(50);

        return view('admin.cars', compact('cars'));
    }

    public function scrape()
    {
        return view('admin.scrape');
    }

    public function runScraper(Request $request, ScraperManager $scraperManager)
    {
        try {
            $sources = $request->input('sources', []);
            $isDryRun = $request->boolean('dry_run', false);
            $save = $request->boolean('save_to_db', true);

            // If dry run, don't save
            if ($isDryRun) {
                $save = false;
            }

            // If no sources selected, use all
            if (empty($sources)) {
                $sources = $scraperManager->getAvailableScrapers();
            }

            // Run scrapers
            $results = $scraperManager->runScrapers($sources, $save);
            $summary = $scraperManager->getSummary();

            $message = "Scraper completed! Total cars: {$summary['total_cars']}, " .
                      "Successful: {$summary['scrapers_succeeded']}, " .
                      "Failed: {$summary['scrapers_failed']}";

            if ($isDryRun) {
                $message .= " (DRY RUN - NOT SAVED)";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Scraper failed: ' . $e->getMessage());
        }
    }
}
