@extends('layouts.app')

@section('title', 'Manage Cars - Admin')

@section('styles')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .search-box {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .search-box input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .cars-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .cars-table table {
        width: 100%;
        border-collapse: collapse;
    }
    .cars-table th,
    .cars-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    .cars-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #666;
    }
    .cars-table tr:hover {
        background: #f8f9fa;
    }
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
    }
    .pagination .active {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
</style>
@endsection

@section('content')
<div class="admin-header">
    <h1>Manage Cars</h1>
    <a href="{{ route('admin.index') }}" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<div class="search-box">
    <form method="GET" action="{{ route('admin.cars') }}">
        <input type="text" name="search" placeholder="Search by make, model, or VIN..." value="{{ request('search') }}">
    </form>
</div>

<div class="cars-table">
    @if($cars->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Year</th>
                    <th>Price</th>
                    <th>Mileage</th>
                    <th>Source</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cars as $car)
                    <tr>
                        <td>{{ $car->id }}</td>
                        <td>{{ $car->make }} {{ $car->model }}</td>
                        <td>{{ $car->year }}</td>
                        <td>${{ number_format($car->price, 0) }}</td>
                        <td>{{ $car->mileage ? number_format($car->mileage) . ' mi' : '-' }}</td>
                        <td>{{ ucfirst($car->source_website) }}</td>
                        <td>{{ $car->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('cars.show', $car) }}" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 1rem;">
            <div class="pagination">
                {{ $cars->links() }}
            </div>
        </div>
    @else
        <div style="padding: 2rem; text-align: center;">
            <p>No cars found.</p>
        </div>
    @endif
</div>
@endsection
