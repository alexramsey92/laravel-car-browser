@extends('layouts.app')

@section('title', 'Run Scraper - Admin')

@section('styles')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .scraper-container {
        display: grid;
        gap: 2rem;
    }

    .scraper-panel {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .scraper-panel h2 {
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        color: #333;
    }

    .scraper-info {
        background: #f8f9fa;
        padding: 1.25rem;
        border-radius: 6px;
        margin-bottom: 2rem;
        border-left: 4px solid #007bff;
    }

    .scraper-info h3 {
        margin-bottom: 0.75rem;
        color: #333;
        font-weight: 600;
    }

    .scraper-info ul {
        margin-left: 1.5rem;
        margin-bottom: 0;
    }

    .scraper-info li {
        margin-bottom: 0.4rem;
        color: #555;
    }

    .scraper-sources {
        margin-bottom: 2rem;
    }

    .scraper-sources label {
        display: block;
        margin-bottom: 1rem;
        font-weight: 600;
        color: #333;
    }

    .source-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .source-checkbox {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9f9f9;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .source-checkbox:hover {
        background: #f0f0f0;
        border-color: #007bff;
    }

    .source-checkbox input[type="checkbox"] {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .source-checkbox input[type="checkbox"]:checked + .source-label {
        font-weight: 600;
        color: #007bff;
    }

    .source-label {
        margin: 0;
        cursor: pointer;
        flex: 1;
    }

    .scraper-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: #545b62;
        text-decoration: none;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background: #138496;
    }

    .warning-box {
        margin-top: 2rem;
        padding: 1.25rem;
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 6px;
        color: #856404;
    }

    .warning-box strong {
        color: #333;
    }

    .info-box {
        padding: 1rem;
        background: #cfe2ff;
        border: 1px solid #b6d4fe;
        border-radius: 6px;
        color: #084298;
        margin-bottom: 1.5rem;
    }

    .tips {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 1.5rem;
    }

    .tips h4 {
        margin-top: 0;
        color: #1565c0;
    }

    .tips ul {
        margin-bottom: 0;
        margin-left: 1.5rem;
    }

    .tips li {
        margin-bottom: 0.3rem;
        color: #0d47a1;
    }

    .loading {
        display: none;
        text-align: center;
        margin: 2rem 0;
    }

    .spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="admin-header">
    <h1>🚗 Car Scraper Control</h1>
    <a href="{{ route('admin.index') }}" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<div class="scraper-container">
    <!-- Quick Start Panel -->
    <div class="scraper-panel">
        <h2>Quick Start</h2>
        <form method="POST" action="{{ route('admin.scrape.run') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="source" value="test">
            <button type="submit" class="btn btn-primary">▶️ Run Test Scraper</button>
        </form>
        <p style="margin-top: 0.75rem; color: #666;">Run the test scraper to see sample data (3 cars)</p>
    </div>

    <!-- Main Scraper Panel -->
    <div class="scraper-panel">
        <h2>Advanced Scraper Options</h2>

        @if ($errors->any())
            <div class="info-box" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                <strong>Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.scrape.run') }}" id="scraperForm">
            @csrf

            <!-- Source Selection -->
            <div class="scraper-sources">
                <label>Select Scraper Sources:</label>
                
                <div class="source-grid">
                    <label class="source-checkbox">
                        <input type="checkbox" name="sources[]" value="test" checked>
                        <p class="source-label">🧪 Test Scraper</p>
                    </label>
                    <label class="source-checkbox">
                        <input type="checkbox" name="sources[]" value="cars.com">
                        <p class="source-label">🚙 Cars.com</p>
                    </label>
                </div>
            </div>

            <!-- Options -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9f9f9; border-radius: 6px;">
                <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; cursor: pointer;">
                    <input type="checkbox" name="save_to_db" value="1" checked>
                    <span>Save results to database</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="dry_run" value="1">
                    <span>Dry run (preview only, don't save)</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="scraper-actions">
                <button type="submit" class="btn btn-primary">▶️ Run Selected Scrapers</button>
                <button type="button" class="btn btn-secondary" onclick="selectAll()">Select All</button>
                <button type="button" class="btn btn-secondary" onclick="deselectAll()">Deselect All</button>
                <a href="{{ route('admin.cars') }}" class="btn btn-info">📋 View All Cars</a>
            </div>
        </form>

        <!-- Tips -->
        <div class="tips">
            <h4>💡 Tips:</h4>
            <ul>
                <li><strong>Test Scraper:</strong> Perfect for testing, returns sample data instantly</li>
                <li><strong>Dry Run:</strong> Preview results without saving to database</li>
                <li><strong>Multiple Sources:</strong> Select multiple to run all at once</li>
                <li><strong>Command Line:</strong> Use <code>php artisan cars:scrape --list</code> to see all available scrapers</li>
            </ul>
        </div>

        <!-- Warning -->
        <div class="warning-box">
            <strong>⚠️ Important:</strong> Running scrapers may take several minutes to complete. Please be patient and do not refresh the page while the scraper is running.
        </div>
    </div>

    <!-- Available Scrapers Info -->
    <div class="scraper-panel">
        <h2>Available Scrapers</h2>
        
        <div class="scraper-info">
            <h3>🧪 Test Scraper</h3>
            <p><strong>Status:</strong> ✅ Ready to use</p>
            <p><strong>Returns:</strong> 3 sample cars (Tesla Model 3, Toyota Camry, Ford F-150)</p>
            <p><strong>Use Case:</strong> Development, testing, and demonstrations</p>
        </div>

        <div class="scraper-info">
            <h3>🌐 Cars.com Scraper</h3>
            <p><strong>Status:</strong> 🔨 In Development (Template)</p>
            <p><strong>Purpose:</strong> Scrapes listings from Cars.com</p>
            <p><strong>Note:</strong> This is an example implementation. CSS selectors may need adjustment for current site structure.</p>
        </div>

        <div class="info-box" style="background: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
            <strong>📚 Learn More:</strong> Check <code>SCRAPER_GUIDE.md</code> for detailed documentation on creating custom scrapers.
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('input[name="sources[]"]').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('input[name="sources[]"]').forEach(cb => cb.checked = false);
}

document.getElementById('scraperForm').addEventListener('submit', function() {
    // Show loading state
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = '⏳ Running...';
});
</script>
@endsection
