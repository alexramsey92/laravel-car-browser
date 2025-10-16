<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'model',
        'year',
        'price',
        'mileage',
        'color',
        'transmission',
        'fuel_type',
        'body_type',
        'description',
        'source_url',
        'source_website',
        'image_url',
        'location',
        'vin',
        'dealer_name',
        'posted_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mileage' => 'integer',
        'year' => 'integer',
        'posted_date' => 'datetime',
    ];

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedMileageAttribute()
    {
        return number_format($this->mileage) . ' miles';
    }
}
