<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Laravel Car Browser'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            font-size: 1.5rem;
        }
        header h1 a {
            color: white;
            text-decoration: none;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
        }
        nav a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #3498db;
        }
        main {
            padding: 2rem 0;
            min-height: calc(100vh - 200px);
        }
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="<?php echo e(route('home')); ?>">🚗 Laravel Car Browser</a></h1>
            <nav>
                <ul>
                    <li><a href="<?php echo e(route('cars.index')); ?>">Browse Cars</a></li>
                    <li><a href="<?php echo e(route('admin.index')); ?>">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-error">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo e(date('Y')); ?> Laravel Car Browser. All rights reserved.</p>
        </div>
    </footer>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/runner/work/laravel-car-browser/laravel-car-browser/resources/views/layouts/app.blade.php ENDPATH**/ ?>