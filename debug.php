<?php
// Debug script to check Laravel configuration

echo "<h1>PrixRetro Debug Info</h1>";

echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Required: 8.2+<br><br>";

echo "<h2>File Checks</h2>";

$baseDir = __DIR__;
echo "Base Directory: $baseDir<br><br>";

// Check .env
$envPath = $baseDir . '/.env';
echo ".env exists: " . (file_exists($envPath) ? "✅ YES" : "❌ NO") . "<br>";
if (file_exists($envPath)) {
    echo ".env readable: " . (is_readable($envPath) ? "✅ YES" : "❌ NO") . "<br>";
    echo ".env size: " . filesize($envPath) . " bytes<br>";
}
echo "<br>";

// Check storage directory
$storagePath = $baseDir . '/storage';
echo "storage/ exists: " . (file_exists($storagePath) ? "✅ YES" : "❌ NO") . "<br>";
if (file_exists($storagePath)) {
    echo "storage/ writable: " . (is_writable($storagePath) ? "✅ YES" : "❌ NO") . "<br>";
    echo "storage/ permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "<br>";
}
echo "<br>";

// Check bootstrap/cache
$bootstrapCache = $baseDir . '/bootstrap/cache';
echo "bootstrap/cache exists: " . (file_exists($bootstrapCache) ? "✅ YES" : "❌ NO") . "<br>";
if (file_exists($bootstrapCache)) {
    echo "bootstrap/cache writable: " . (is_writable($bootstrapCache) ? "✅ YES" : "❌ NO") . "<br>";
    echo "bootstrap/cache permissions: " . substr(sprintf('%o', fileperms($bootstrapCache)), -4) . "<br>";
}
echo "<br>";

// Check public/index.php
$publicIndex = $baseDir . '/public/index.php';
echo "public/index.php exists: " . (file_exists($publicIndex) ? "✅ YES" : "❌ NO") . "<br>";
echo "<br>";

// Check vendor directory
$vendorPath = $baseDir . '/vendor';
echo "vendor/ exists: " . (file_exists($vendorPath) ? "✅ YES" : "❌ NO") . "<br>";
if (file_exists($vendorPath)) {
    $vendorAutoload = $vendorPath . '/autoload.php';
    echo "vendor/autoload.php exists: " . (file_exists($vendorAutoload) ? "✅ YES" : "❌ NO") . "<br>";
}
echo "<br>";

echo "<h2>PHP Extensions</h2>";
$required = ['mbstring', 'xml', 'ctype', 'json', 'openssl', 'pdo', 'pdo_mysql', 'fileinfo', 'tokenizer'];
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "$ext: " . ($loaded ? "✅ YES" : "❌ NO") . "<br>";
}

echo "<h2>Environment Variables (from .env)</h2>";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            list($key, $value) = explode('=', $line, 2);
            // Hide sensitive values
            if (str_contains($key, 'PASSWORD') || str_contains($key, 'KEY') || str_contains($key, 'SECRET')) {
                $value = '***HIDDEN***';
            }
            echo htmlspecialchars($key) . " = " . htmlspecialchars($value) . "<br>";
        }
    }
}
