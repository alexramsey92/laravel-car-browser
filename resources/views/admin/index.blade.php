@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-card h3 {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #3498db;
    }
    .admin-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .admin-nav-item {
        background: white;
        padding: 1.5rem;
        text-align: center;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .admin-nav-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .admin-nav-item h3 {
        margin-bottom: 0.5rem;
        color: #3498db;
    }
    .recent-cars {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .recent-cars h2 {
        margin-bottom: 1rem;
    }
    .recent-cars table {
        width: 100%;
        border-collapse: collapse;
    }
    .recent-cars th,
    .recent-cars td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    .recent-cars th {
        font-weight: 600;
        color: #666;
    }
</style>
@endsection

@section('content')
<h1 style="margin-bottom: 2rem;">Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Cars</h3>
        <div class="stat-value">{{ number_format($stats['total_cars']) }}</div>
    </div>
    <div class="stat-card">
        <h3>Total Makes</h3>
        <div class="stat-value">{{ $stats['total_makes'] }}</div>
    </div>
    <div class="stat-card">
        <h3>Average Price</h3>
        <div class="stat-value">${{ number_format($stats['avg_price'], 0) }}</div>
    </div>
</div>

<div class="admin-nav">
    <a href="{{ route('admin.cars') }}" class="admin-nav-item">
        <h3>Manage Cars</h3>
        <p>View and manage all car listings</p>
    </a>
    <a href="{{ route('admin.scrape') }}" class="admin-nav-item">
        <h3>Run Scraper</h3>
        <p>Scrape new car listings from sources</p>
    </a>
</div>

<div class="recent-cars">
    <h2>Recently Added Cars</h2>
    @if($stats['latest_cars']->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Price</th>
                    <th>Source</th>
                    <th>Added</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['latest_cars'] as $car)
                    <tr>
                        <td>{{ $car->year }} {{ $car->make }} {{ $car->model }}</td>
                        <td>${{ number_format($car->price, 0) }}</td>
                        <td>{{ ucfirst($car->source_website) }}</td>
                        <td>{{ $car->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No cars have been added yet. Run the scraper to start collecting listings.</p>
    @endif
</div>
@endsection
