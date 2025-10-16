<?php

namespace App\Http\Controllers;

use App\Models\Car;
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

    public function runScraper(Request $request)
    {
        try {
            Artisan::call('cars:scrape');
            $output = Artisan::output();

            return back()->with('success', 'Scraper executed successfully! ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Scraper failed: ' . $e->getMessage());
        }
    }
}
