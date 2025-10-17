<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->decimal('price', 12, 2);
            $table->integer('mileage')->nullable();
            $table->string('color')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('body_type')->nullable();
            $table->text('description')->nullable();
            $table->string('source_url')->unique();
            $table->string('source_website');
            $table->string('image_url')->nullable();
            $table->string('location')->nullable();
            $table->string('vin')->nullable();
            $table->string('dealer_name')->nullable();
            $table->timestamp('posted_date')->nullable();
            $table->timestamps();
            
            $table->index(['make', 'model', 'year']);
            $table->index('price');
            $table->index('source_website');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
