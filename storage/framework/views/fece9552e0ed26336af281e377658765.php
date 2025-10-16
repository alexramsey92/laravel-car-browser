<?php $__env->startSection('title', 'Browse Cars'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .filters {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filters h2 {
        margin-bottom: 1rem;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    .filter-group label {
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    .filter-group input,
    .filter-group select {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .car-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }
    .car-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .car-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .car-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    .car-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .car-details {
        padding: 1.5rem;
    }
    .car-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .car-price {
        color: #27ae60;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .car-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .car-info-item {
        display: flex;
        justify-content: space-between;
        color: #666;
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
    .no-cars {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 8px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="filters">
    <h2>Filter Cars</h2>
    <form method="GET" action="<?php echo e(route('cars.index')); ?>">
        <div class="filter-grid">
            <div class="filter-group">
                <label for="make">Make</label>
                <select name="make" id="make">
                    <option value="">All Makes</option>
                    <?php $__currentLoopData = $makes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $make): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($make); ?>" <?php echo e(request('make') == $make ? 'selected' : ''); ?>>
                            <?php echo e($make); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" value="<?php echo e(request('model')); ?>" placeholder="e.g., Camry">
            </div>
            <div class="filter-group">
                <label for="year_from">Year From</label>
                <input type="number" name="year_from" id="year_from" value="<?php echo e(request('year_from')); ?>" placeholder="2015">
            </div>
            <div class="filter-group">
                <label for="year_to">Year To</label>
                <input type="number" name="year_to" id="year_to" value="<?php echo e(request('year_to')); ?>" placeholder="2024">
            </div>
            <div class="filter-group">
                <label for="price_from">Price From</label>
                <input type="number" name="price_from" id="price_from" value="<?php echo e(request('price_from')); ?>" placeholder="10000">
            </div>
            <div class="filter-group">
                <label for="price_to">Price To</label>
                <input type="number" name="price_to" id="price_to" value="<?php echo e(request('price_to')); ?>" placeholder="50000">
            </div>
        </div>
        <div style="margin-top: 1rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn">Apply Filters</button>
            <a href="<?php echo e(route('cars.index')); ?>" class="btn btn-secondary">Clear Filters</a>
        </div>
    </form>
</div>

<h2 style="margin-bottom: 1rem;">Available Cars (<?php echo e($cars->total()); ?>)</h2>

<?php if($cars->count() > 0): ?>
    <div class="car-grid">
        <?php $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="car-card">
                <div class="car-image">
                    <?php if($car->image_url): ?>
                        <img src="<?php echo e($car->image_url); ?>" alt="<?php echo e($car->make); ?> <?php echo e($car->model); ?>">
                    <?php else: ?>
                        <span>No Image</span>
                    <?php endif; ?>
                </div>
                <div class="car-details">
                    <div class="car-title"><?php echo e($car->year); ?> <?php echo e($car->make); ?> <?php echo e($car->model); ?></div>
                    <div class="car-price">$<?php echo e(number_format($car->price, 0)); ?></div>
                    <div class="car-info">
                        <?php if($car->mileage): ?>
                            <div class="car-info-item">
                                <span>Mileage:</span>
                                <strong><?php echo e(number_format($car->mileage)); ?> mi</strong>
                            </div>
                        <?php endif; ?>
                        <?php if($car->color): ?>
                            <div class="car-info-item">
                                <span>Color:</span>
                                <strong><?php echo e($car->color); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if($car->transmission): ?>
                            <div class="car-info-item">
                                <span>Transmission:</span>
                                <strong><?php echo e($car->transmission); ?></strong>
                            </div>
                        <?php endif; ?>
                        <div class="car-info-item">
                            <span>Source:</span>
                            <strong><?php echo e(ucfirst($car->source_website)); ?></strong>
                        </div>
                    </div>
                    <a href="<?php echo e(route('cars.show', $car)); ?>" class="btn" style="width: 100%; text-align: center;">View Details</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="pagination">
        <?php echo e($cars->links()); ?>

    </div>
<?php else: ?>
    <div class="no-cars">
        <h3>No cars found</h3>
        <p>Try adjusting your filters or check back later.</p>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/laravel-car-browser/laravel-car-browser/resources/views/cars/index.blade.php ENDPATH**/ ?>