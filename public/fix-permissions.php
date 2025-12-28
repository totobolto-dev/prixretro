<?php
/**
 * Fix Laravel file permissions for production
 * DELETE THIS FILE AFTER USE!
 */

if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL to execute');
}

echo "<h1>Fixing Permissions...</h1>";

$basePath = dirname(__DIR__);
$errors = [];

// Directories that need to be writable
$writableDirs = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    
    if (!file_exists($fullPath)) {
        @mkdir($fullPath, 0755, true);
        echo "<p>✅ Created: {$dir}</p>";
    }
    
    if (is_writable($fullPath)) {
        echo "<p>✅ Writable: {$dir}</p>";
    } else {
        @chmod($fullPath, 0755);
        if (is_writable($fullPath)) {
            echo "<p>✅ Fixed permissions: {$dir}</p>";
        } else {
            echo "<p style='color:red'>❌ Cannot write: {$dir}</p>";
            $errors[] = $dir;
        }
    }
}

echo "<hr>";

if (empty($errors)) {
    echo "<h2 style='color:green'>✅ All permissions OK!</h2>";
} else {
    echo "<h2 style='color:red'>❌ Some directories still not writable:</h2>";
    echo "<ul>";
    foreach ($errors as $dir) {
        echo "<li>{$dir}</li>";
    }
    echo "</ul>";
    echo "<p>You may need to fix these manually via FTP/SSH</p>";
}

echo "<hr>";
echo "<p><strong style='color: red;'>⚠️ DELETE THIS FILE NOW!</strong></p>";
