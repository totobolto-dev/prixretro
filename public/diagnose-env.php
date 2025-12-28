<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Diagnosing .env File</h1>";

$envPath = dirname(__DIR__) . '/.env';

echo "<h2>File Check</h2>";
echo "<p><strong>Path:</strong> {$envPath}</p>";
echo "<p><strong>Exists:</strong> " . (file_exists($envPath) ? '✅ YES' : '❌ NO') . "</p>";
echo "<p><strong>Readable:</strong> " . (is_readable($envPath) ? '✅ YES' : '❌ NO') . "</p>";
echo "<p><strong>Size:</strong> " . (file_exists($envPath) ? filesize($envPath) . ' bytes' : 'N/A') . "</p>";
echo "<p><strong>Permissions:</strong> " . (file_exists($envPath) ? substr(sprintf('%o', fileperms($envPath)), -4) : 'N/A') . "</p>";

if (file_exists($envPath)) {
    echo "<h2>First 20 Lines</h2>";
    echo "<pre style='background: #f0f0f0; padding: 20px;'>";
    $lines = file($envPath);
    echo htmlspecialchars(implode('', array_slice($lines, 0, 20)));
    echo "</pre>";
    
    echo "<h2>Manual Parse Test</h2>";
    $content = file_get_contents($envPath);
    $parsed = [];
    foreach (explode("\n", $content) as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $parsed[trim($key)] = trim($value);
        }
    }
    
    echo "<p>Parsed " . count($parsed) . " variables:</p>";
    echo "<pre style='background: #f0f0f0; padding: 20px; max-height: 300px; overflow: auto;'>";
    foreach ($parsed as $key => $value) {
        if (strpos($key, 'PASSWORD') === false && strpos($key, 'KEY') === false) {
            echo htmlspecialchars("{$key} = {$value}") . "\n";
        } else {
            echo htmlspecialchars("{$key} = [HIDDEN]") . "\n";
        }
    }
    echo "</pre>";
}

echo "<hr>";
echo "<h2>Try Loading with Dotenv</h2>";

try {
    require dirname(__DIR__) . '/vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    
    echo "<p style='color: green;'>✅ Dotenv loaded successfully!</p>";
    echo "<p>APP_ENV from env(): " . (env('APP_ENV') ?: 'NOT SET') . "</p>";
    echo "<p>APP_DEBUG from env(): " . (env('APP_DEBUG') ?: 'NOT SET') . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Dotenv failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
