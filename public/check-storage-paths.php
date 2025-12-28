<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Checking Storage Paths</h1>";

$basePath = dirname(__DIR__);

$paths = [
    'storage' => $basePath . '/storage',
    'storage/framework' => $basePath . '/storage/framework',
    'storage/framework/cache' => $basePath . '/storage/framework/cache',
    'storage/framework/cache/data' => $basePath . '/storage/framework/cache/data',
    'storage/framework/views' => $basePath . '/storage/framework/views',
    'storage/framework/sessions' => $basePath . '/storage/framework/sessions',
    'storage/logs' => $basePath . '/storage/logs',
    'bootstrap/cache' => $basePath . '/bootstrap/cache',
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Path</th><th>Exists</th><th>Writable</th><th>Permissions</th><th>Files</th></tr>";

foreach ($paths as $name => $path) {
    $exists = file_exists($path);
    $writable = is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

    $files = 0;
    if ($exists && is_dir($path)) {
        $items = scandir($path);
        $files = count($items) - 2; // Exclude . and ..
    }

    echo "<tr>";
    echo "<td><strong>{$name}</strong><br><small style='color: gray;'>{$path}</small></td>";
    echo "<td>" . ($exists ? '✅' : '<span style="color: red;">❌</span>') . "</td>";
    echo "<td>" . ($writable ? '✅' : '<span style="color: red;">❌</span>') . "</td>";
    echo "<td>{$perms}</td>";
    echo "<td>{$files} items</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Try Creating Missing Directories</h2>";

$created = [];
$failed = [];

foreach ($paths as $name => $path) {
    if (!file_exists($path)) {
        if (@mkdir($path, 0755, true)) {
            $created[] = $name;
        } else {
            $failed[] = $name;
        }
    }
}

if (!empty($created)) {
    echo "<p style='color: green;'>✅ Created: " . implode(', ', $created) . "</p>";
}

if (!empty($failed)) {
    echo "<p style='color: red;'>❌ Failed to create: " . implode(', ', $failed) . "</p>";
}

if (empty($created) && empty($failed)) {
    echo "<p>All directories already exist ✅</p>";
}

echo "<hr>";
echo "<h2>Check .env Cache Paths</h2>";

require $basePath . '/vendor/autoload.php';

try {
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/', 'GET');
    $kernel->handle($request);

    $cachePath = config('cache.path');
    $viewPath = config('view.compiled');

    echo "<p><strong>CACHE_PATH from config:</strong> " . ($cachePath ?: 'NOT SET') . "</p>";
    echo "<p><strong>VIEW_COMPILED_PATH from config:</strong> " . ($viewPath ?: 'NOT SET') . "</p>";

    if ($cachePath) {
        echo "<p>Cache path exists: " . (file_exists($cachePath) ? '✅' : '❌') . "</p>";
        echo "<p>Cache path writable: " . (is_writable($cachePath) ? '✅' : '❌') . "</p>";
    }

    if ($viewPath) {
        echo "<p>View path exists: " . (file_exists($viewPath) ? '✅' : '❌') . "</p>";
        echo "<p>View path writable: " . (is_writable($viewPath) ? '✅' : '❌') . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
