@extends('layouts.app')

@section('title', $car->year . ' ' . $car->make . ' ' . $car->model)

@section('styles')
<style>
    .car-detail {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .car-detail-image {
        width: 100%;
        height: 400px;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    .car-detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .car-detail-content {
        padding: 2rem;
    }
    .car-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }
    .car-detail-title {
        font-size: 2rem;
        font-weight: bold;
    }
    .car-detail-price {
        font-size: 2rem;
        color: #27ae60;
        font-weight: bold;
    }
    .car-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .car-detail-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .car-detail-label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .car-detail-value {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .car-description {
        margin-bottom: 2rem;
    }
    .car-description h3 {
        margin-bottom: 1rem;
    }
    .car-actions {
        display: flex;
        gap: 1rem;
    }
</style>
@endsection

@section('content')
<a href="{{ route('cars.index') }}" class="btn btn-secondary" style="margin-bottom: 1rem;">← Back to Listings</a>

<div class="car-detail">
    <div class="car-detail-image">
        @if($car->image_url)
            <img src="{{ $car->image_url }}" alt="{{ $car->make }} {{ $car->model }}">
        @else
            <span>No Image Available</span>
        @endif
    </div>
    <div class="car-detail-content">
        <div class="car-detail-header">
            <div class="car-detail-title">
                {{ $car->year }} {{ $car->make }} {{ $car->model }}
            </div>
            <div class="car-detail-price">
                ${{ number_format($car->price, 0) }}
            </div>
        </div>

        <div class="car-detail-grid">
            @if($car->mileage)
                <div class="car-detail-item">
                    <div class="car-detail-label">Mileage</div>
                    <div class="car-detail-value">{{ number_format($car->mileage) }} miles</div>
                </div>
            @endif
            @if($car->color)
                <div class="car-detail-item">
                    <div class="car-detail-label">Color</div>
                    <div class="car-detail-value">{{ $car->color }}</div>
                </div>
            @endif
            @if($car->transmission)
                <div class="car-detail-item">
                    <div class="car-detail-label">Transmission</div>
                    <div class="car-detail-value">{{ $car->transmission }}</div>
                </div>
            @endif
            @if($car->fuel_type)
                <div class="car-detail-item">
                    <div class="car-detail-label">Fuel Type</div>
                    <div class="car-detail-value">{{ $car->fuel_type }}</div>
                </div>
            @endif
            @if($car->body_type)
                <div class="car-detail-item">
                    <div class="car-detail-label">Body Type</div>
                    <div class="car-detail-value">{{ $car->body_type }}</div>
                </div>
            @endif
            @if($car->location)
                <div class="car-detail-item">
                    <div class="car-detail-label">Location</div>
                    <div class="car-detail-value">{{ $car->location }}</div>
                </div>
            @endif
            @if($car->dealer_name)
                <div class="car-detail-item">
                    <div class="car-detail-label">Dealer</div>
                    <div class="car-detail-value">{{ $car->dealer_name }}</div>
                </div>
            @endif
            @if($car->vin)
                <div class="car-detail-item">
                    <div class="car-detail-label">VIN</div>
                    <div class="car-detail-value">{{ $car->vin }}</div>
                </div>
            @endif
            <div class="car-detail-item">
                <div class="car-detail-label">Source</div>
                <div class="car-detail-value">{{ ucfirst($car->source_website) }}</div>
            </div>
        </div>

        @if($car->description)
            <div class="car-description">
                <h3>Description</h3>
                <p>{{ $car->description }}</p>
            </div>
        @endif

        <div class="car-actions">
            <a href="{{ $car->source_url }}" target="_blank" class="btn">View on {{ ucfirst($car->source_website) }}</a>
        </div>
    </div>
</div>
@endsection
