<?php
echo "<h1>Admin Route Test</h1>";

// Test 1: Can we access this file?
echo "<p>✅ PHP execution works</p>";

// Test 2: Check .htaccess
$htaccess = file_exists(__DIR__ . '/.htaccess');
echo "<p>.htaccess exists: " . ($htaccess ? '✅ YES' : '❌ NO') . "</p>";

// Test 3: Check Laravel bootstrap
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    echo "<p>✅ Laravel bootstraps OK</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Laravel bootstrap failed: " . $e->getMessage() . "</p>";
}

// Test 4: Check if we can simulate admin access
echo "<hr>";
echo "<h2>Testing Routes</h2>";

// Simulate requests to different paths
$testUrls = [
    '/admin' => 'Admin root',
    '/admin/login' => 'Admin login',
    '/admin/dashboard' => 'Admin dashboard',
];

foreach ($testUrls as $path => $label) {
    echo "<p>{$label} ({$path}): <a href='{$path}' target='_blank'>Test Link</a></p>";
}

echo "<hr>";
echo "<h2>Server Info</h2>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
