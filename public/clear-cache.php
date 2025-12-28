<?php
/**
 * Clear all Laravel caches
 * DELETE AFTER USE!
 */

if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Clearing Laravel Caches...</h1>";

$basePath = dirname(__DIR__);

// Files to delete
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/routes.php',
];

foreach ($cacheFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "<p style='color: green;'>✅ Deleted: {$file}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to delete: {$file}</p>";
        }
    } else {
        echo "<p style='color: gray;'>⚪ Not found: {$file}</p>";
    }
}

// Clear directories
$cacheDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    
    if (is_dir($fullPath)) {
        $files = glob($fullPath . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p style='color: green;'>✅ Cleared {$count} files from: {$dir}</p>";
    }
}

echo "<hr>";
echo "<h2 style='color: green;'>✅ Cache Cleared!</h2>";
echo "<p><strong>Now try:</strong></p>";
echo "<ol>";
echo "<li><a href='/check-env.php?allow=1'>Check if .env loads</a></li>";
echo "<li><a href='/admin'>Login to admin</a></li>";
echo "</ol>";
echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
