<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Tracing Laravel Bootstrap Process</h1>";

$basePath = dirname(__DIR__);

echo "<h2>Step 1: Check Paths</h2>";
echo "<p><strong>Base Path:</strong> {$basePath}</p>";
echo "<p><strong>.env Path:</strong> {$basePath}/.env</p>";
echo "<p><strong>.env Exists:</strong> " . (file_exists($basePath . '/.env') ? '✅ YES' : '❌ NO') . "</p>";

echo "<h2>Step 2: Check for Cached Config</h2>";
$cachedConfig = $basePath . '/bootstrap/cache/config.php';
$configCached = file_exists($cachedConfig);
echo "<p><strong>bootstrap/cache/config.php exists:</strong> " . ($configCached ? '✅ YES (THIS IS WHY!)' : '❌ NO') . "</p>";

if ($configCached) {
    echo "<p style='color: red; font-size: 18px;'><strong>FOUND IT!</strong> Config is cached. Laravel won't load .env!</p>";
    echo "<p>File size: " . filesize($cachedConfig) . " bytes</p>";
    echo "<p>Modified: " . date('Y-m-d H:i:s', filemtime($cachedConfig)) . "</p>";
}

echo "<h2>Step 3: Check Bootstrap Directory Permissions</h2>";
$bootstrapCache = $basePath . '/bootstrap/cache';
echo "<p><strong>Directory exists:</strong> " . (is_dir($bootstrapCache) ? '✅ YES' : '❌ NO') . "</p>";
echo "<p><strong>Writable:</strong> " . (is_writable($bootstrapCache) ? '✅ YES' : '❌ NO') . "</p>";

if (is_dir($bootstrapCache)) {
    $files = scandir($bootstrapCache);
    echo "<p><strong>Files in bootstrap/cache:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullPath = $bootstrapCache . '/' . $file;
        echo "<li>{$file} (" . filesize($fullPath) . " bytes, modified " . date('Y-m-d H:i:s', filemtime($fullPath)) . ")";
        if ($file === 'config.php') {
            echo " <strong style='color: red;'>← THIS PREVENTS .env LOADING!</strong>";
        }
        echo "</li>";
    }
    echo "</ul>";
}

echo "<h2>Step 4: Check External Environment Variables</h2>";
echo "<p>These can override .env file:</p>";
echo "<ul>";
$externalVars = ['APP_ENV', 'APP_DEBUG', 'APP_KEY', 'DB_DATABASE'];
foreach ($externalVars as $var) {
    $serverVal = $_SERVER[$var] ?? null;
    $envVal = $_ENV[$var] ?? null;

    if ($serverVal || $envVal) {
        echo "<li><strong>{$var}:</strong> ";
        echo "SERVER=" . ($serverVal ?: 'null') . ", ENV=" . ($envVal ?: 'null');
        echo " <strong style='color: orange;'>← Set externally!</strong>";
        echo "</li>";
    } else {
        echo "<li>{$var}: Not set externally ✅</li>";
    }
}
echo "</ul>";

echo "<h2>Step 5: Try Manual Bootstrap</h2>";
echo "<p>Attempting to manually load Laravel Application...</p>";

try {
    require $basePath . '/vendor/autoload.php';

    // Check if LoadEnvironmentVariables class exists
    $className = 'Illuminate\\Foundation\\Bootstrap\\LoadEnvironmentVariables';
    if (!class_exists($className)) {
        echo "<p style='color: red;'>❌ {$className} not found!</p>";
    } else {
        echo "<p style='color: green;'>✅ {$className} exists</p>";
    }

    // Create minimal app instance
    $app = require_once $basePath . '/bootstrap/app.php';

    echo "<p style='color: green;'>✅ Application loaded</p>";
    echo "<p><strong>environmentPath():</strong> " . $app->environmentPath() . "</p>";
    echo "<p><strong>environmentFile():</strong> " . $app->environmentFile() . "</p>";
    echo "<p><strong>configurationIsCached():</strong> " . ($app->configurationIsCached() ? 'TRUE (PROBLEM!)' : 'FALSE') . "</p>";

    if ($app->configurationIsCached()) {
        echo "<p style='color: red; font-size: 18px;'><strong>THIS IS THE PROBLEM!</strong></p>";
        echo "<p>Laravel thinks config is cached, so it's not loading .env</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
