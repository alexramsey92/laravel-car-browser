#!/bin/bash

# Laravel Car Browser Setup Script
# This script sets up the Laravel application and creates necessary directories

echo "🚗 Setting up Laravel Car Browser..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the Laravel project root directory"
    exit 1
fi

# Create necessary storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache/data
mkdir -p storage/app/public
mkdir -p storage/logs

# Check if .env file exists, if not copy from .env.example
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please update your .env file with your specific configuration"
else
    echo "✅ .env file already exists"
fi

# Generate application key if not set
echo "🔑 Generating application key..."
php artisan key:generate

# Set proper permissions for storage and bootstrap/cache directories
echo "🔒 Setting proper permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# Remove any problematic cache files
echo "🗑️  Removing problematic cache files..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/events.php

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache

# Check if database exists (for SQLite)
if grep -q "DB_CONNECTION=sqlite" .env; then
    if [ ! -f "database/database.sqlite" ]; then
        echo "🗄️  Creating SQLite database file..."
        touch database/database.sqlite
    fi
    
    echo "🏗️  Running database migrations..."
    php artisan migrate --force
    
    echo "🌱 Running database seeders..."
    php artisan db:seed --force
fi

echo ""
echo "✅ Setup complete! Your Laravel Car Browser application is ready."
echo ""
echo "📋 Next steps:"
echo "   1. Update your .env file with your specific configuration"
echo "   2. If using a different database, update DB_* settings in .env"
echo "   3. Run 'php artisan serve' to start the development server"
echo ""
echo "🎉 Happy coding!"