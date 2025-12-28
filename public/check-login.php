<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<h1>Login Page Debug</h1>";

// Check if Filament is installed
echo "<h2>Filament Status</h2>";
echo "<p>Filament installed: " . (class_exists('Filament\Facades\Filament') ? '✅ YES' : '❌ NO') . "</p>";

// Check asset paths
echo "<h2>Asset URLs</h2>";
$assetUrl = config('app.url');
echo "<p>APP_URL: {$assetUrl}</p>";
echo "<p>Asset URL should be: {$assetUrl}/js/filament/filament/app.js</p>";

// Check if assets are published
$assetsExist = file_exists(__DIR__ . '/css/filament/filament/app.css');
echo "<p>CSS exists: " . ($assetsExist ? '✅ YES' : '❌ NO') . "</p>";

echo "<hr>";
echo "<p><a href='/admin'>Go to Admin Login</a></p>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
