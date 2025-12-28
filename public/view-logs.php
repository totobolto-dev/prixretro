<?php
if (!isset($_GET['allow'])) {
    die('Add ?allow=1 to URL');
}

$logPath = __DIR__ . '/../storage/logs/laravel.log';

echo "<h1>Laravel Logs (Last 100 lines)</h1>";

if (!file_exists($logPath)) {
    echo "<p style='color:red'>❌ Log file not found: {$logPath}</p>";
    exit;
}

$lines = file($logPath);
$lastLines = array_slice($lines, -100);

echo "<pre style='background: #000; color: #0f0; padding: 20px; overflow: auto; max-height: 600px;'>";
echo htmlspecialchars(implode('', $lastLines));
echo "</pre>";

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
