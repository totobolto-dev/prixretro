<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Verifying Configuration Files</h1>";

$basePath = dirname(__DIR__);

$configFiles = [
    'config/view.php',
    'config/cache.php',
    'config/app.php',
];

echo "<h2>Config Files Status</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>File</th><th>Exists</th><th>Size</th><th>Modified</th></tr>";

foreach ($configFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);

    echo "<tr>";
    echo "<td><strong>{$file}</strong></td>";
    echo "<td>" . ($exists ? '✅' : '<span style="color: red;">❌ MISSING!</span>') . "</td>";
    echo "<td>" . ($exists ? filesize($fullPath) . ' bytes' : 'N/A') . "</td>";
    echo "<td>" . ($exists ? date('Y-m-d H:i:s', filemtime($fullPath)) : 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Load Config Values</h2>";

require $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$kernel->handle($request);

$checks = [
    'app.url' => config('app.url'),
    'app.env' => config('app.env'),
    'app.debug' => config('app.debug'),
    'view.compiled' => config('view.compiled'),
    'cache.default' => config('cache.default'),
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Config Key</th><th>Value</th></tr>";

foreach ($checks as $key => $value) {
    $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;

    echo "<tr>";
    echo "<td><strong>{$key}</strong></td>";
    echo "<td>" . ($displayValue ?: '<span style="color: red;">NULL</span>') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>config/view.php Contents</h2>";

$viewConfigPath = $basePath . '/config/view.php';
if (file_exists($viewConfigPath)) {
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 400px; overflow: auto;'>";
    echo htmlspecialchars(file_get_contents($viewConfigPath));
    echo "</pre>";
} else {
    echo "<p style='color: red; font-size: 18px;'><strong>❌ config/view.php DOES NOT EXIST!</strong></p>";
    echo "<p>This is why you're getting 'Please provide a valid cache path' error!</p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
