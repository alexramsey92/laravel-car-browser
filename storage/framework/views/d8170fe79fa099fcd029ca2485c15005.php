<?php $__env->startSection('title', 'Run Scraper - Admin'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .scraper-panel {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .scraper-panel h2 {
        margin-bottom: 1rem;
    }
    .scraper-info {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
    }
    .scraper-info h3 {
        margin-bottom: 0.5rem;
    }
    .scraper-info ul {
        margin-left: 1.5rem;
    }
    .scraper-info li {
        margin-bottom: 0.25rem;
    }
    .scraper-actions {
        display: flex;
        gap: 1rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header">
    <h1>Run Car Scraper</h1>
    <a href="<?php echo e(route('admin.index')); ?>" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<div class="scraper-panel">
    <h2>Car Scraper Service</h2>
    
    <div class="scraper-info">
        <h3>About the Scraper</h3>
        <p>This scraper collects car listings from multiple sources across the internet, including:</p>
        <ul>
            <li>AutoTrader</li>
            <li>Cars.com</li>
            <li>CarGurus</li>
            <li>CarMax</li>
        </ul>
        <p style="margin-top: 0.5rem;"><strong>Note:</strong> The scraper is configured to run automatically once daily. You can also run it manually using the button below.</p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.scrape.run')); ?>">
        <?php echo csrf_field(); ?>
        <div class="scraper-actions">
            <button type="submit" class="btn">Run Scraper Now</button>
            <a href="<?php echo e(route('admin.cars')); ?>" class="btn btn-secondary">View All Cars</a>
        </div>
    </form>

    <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 4px; color: #856404;">
        <strong>⚠️ Important:</strong> Running the scraper may take several minutes to complete. Please be patient and do not refresh the page.
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/laravel-car-browser/laravel-car-browser/resources/views/admin/scrape.blade.php ENDPATH**/ ?>