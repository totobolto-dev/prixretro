<?php
if (!isset($_GET['allow'])) {
    die('Add ?allow=1 to URL');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<h1>Environment Configuration Check</h1>";

$checks = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'CACHE_PATH' => '/home/pwagrad/prixretro/storage/framework/cache',
    'VIEW_COMPILED_PATH' => '/home/pwagrad/prixretro/storage/framework/views',
];

foreach ($checks as $key => $expected) {
    $actual = env($key);
    $match = $actual == $expected;
    $color = $match ? 'green' : 'red';
    $status = $match ? '✅' : '❌';
    
    echo "<p style='color: {$color}'>";
    echo "<strong>{$key}:</strong> {$status}<br>";
    echo "Expected: <code>{$expected}</code><br>";
    echo "Actual: <code>" . ($actual ?: 'NOT SET') . "</code>";
    echo "</p>";
}

echo "<hr>";
echo "<h2>All Environment Variables</h2>";
echo "<pre style='background: #f0f0f0; padding: 20px; overflow: auto; max-height: 400px;'>";
$envVars = [];
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'PASSWORD') === false && strpos($key, 'KEY') === false) {
        $envVars[$key] = $value;
    }
}
ksort($envVars);
print_r($envVars);
echo "</pre>";

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
