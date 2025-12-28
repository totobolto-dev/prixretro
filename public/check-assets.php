<?php
echo "<h1>Asset Checker</h1>";

$checks = [
    'Filament App JS' => 'js/filament/filament/app.js',
    'Filament CSS' => 'css/filament/filament/app.css',
    'Filament Forms JS' => 'js/filament/forms/components/text-input.js',
];

foreach ($checks as $name => $path) {
    $fullPath = __DIR__ . '/' . $path;
    $exists = file_exists($fullPath);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✅ EXISTS' : '❌ MISSING';
    $size = $exists ? filesize($fullPath) . ' bytes' : 'N/A';
    
    echo "<p style='color: {$color}'><strong>{$name}:</strong> {$status} ({$size})</p>";
    echo "<p style='margin-left: 20px; font-size: 0.9em;'>Path: {$path}</p>";
}

echo "<hr>";
echo "<h2>PHP Info</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . __FILE__ . "</p>";

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE AFTER CHECKING!</strong></p>";
