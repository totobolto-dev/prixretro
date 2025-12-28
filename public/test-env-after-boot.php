<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Testing .env After Full Bootstrap</h1>";

$basePath = dirname(__DIR__);

require $basePath . '/vendor/autoload.php';

echo "<h2>Before Bootstrap</h2>";
echo "<p><strong>APP_ENV from env():</strong> " . (env('APP_ENV') ?: 'NULL') . "</p>";
echo "<p><strong>APP_ENV from \$_ENV:</strong> " . ($_ENV['APP_ENV'] ?? 'NULL') . "</p>";
echo "<p><strong>APP_ENV from getenv():</strong> " . (getenv('APP_ENV') ?: 'NULL') . "</p>";

echo "<hr>";
echo "<h2>Creating Application...</h2>";

$app = require_once $basePath . '/bootstrap/app.php';

echo "<p>✅ Application created</p>";

echo "<h2>After App Creation (Before Boot)</h2>";
echo "<p><strong>APP_ENV from env():</strong> " . (env('APP_ENV') ?: 'NULL') . "</p>";
echo "<p><strong>APP_ENV from \$_ENV:</strong> " . ($_ENV['APP_ENV'] ?? 'NULL') . "</p>";

echo "<hr>";
echo "<h2>Booting Application...</h2>";

try {
    // Create HTTP kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "<p>✅ Kernel created</p>";

    // Create a fake request
    $request = Illuminate\Http\Request::create('/', 'GET');

    echo "<p>✅ Request created</p>";

    // Handle request (this boots the application)
    $response = $kernel->handle($request);

    echo "<p>✅ Application BOOTED</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Boot error: " . $e->getMessage() . "</p>";
    echo "<pre style='font-size: 11px; max-height: 400px; overflow: auto;'>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>After Full Boot</h2>";

$testVars = [
    'APP_ENV',
    'APP_DEBUG',
    'APP_KEY',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'CACHE_PATH',
    'VIEW_COMPILED_PATH',
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Variable</th><th>env()</th><th>\$_ENV</th><th>getenv()</th><th>config()</th></tr>";

foreach ($testVars as $var) {
    $envValue = env($var);
    $serverValue = $_ENV[$var] ?? null;
    $getenvValue = getenv($var);

    // Try to get from config
    $configKey = strtolower(str_replace('_', '.', substr($var, 0, 3))) . '.' . strtolower(substr($var, 4));
    if ($var === 'APP_ENV') $configKey = 'app.env';
    if ($var === 'APP_DEBUG') $configKey = 'app.debug';
    if ($var === 'APP_KEY') $configKey = 'app.key';
    if ($var === 'APP_URL') $configKey = 'app.url';
    if ($var === 'DB_CONNECTION') $configKey = 'database.default';
    if ($var === 'DB_HOST') $configKey = 'database.connections.mysql.host';
    if ($var === 'DB_PORT') $configKey = 'database.connections.mysql.port';
    if ($var === 'DB_DATABASE') $configKey = 'database.connections.mysql.database';
    if ($var === 'CACHE_PATH') $configKey = 'cache.path';
    if ($var === 'VIEW_COMPILED_PATH') $configKey = 'view.compiled';

    try {
        $configValue = config($configKey);
    } catch (Exception $e) {
        $configValue = 'ERROR';
    }

    echo "<tr>";
    echo "<td><strong>{$var}</strong></td>";
    echo "<td>" . ($envValue ?: '<span style="color:red">NULL</span>') . "</td>";
    echo "<td>" . ($serverValue ?: '<span style="color:red">NULL</span>') . "</td>";
    echo "<td>" . ($getenvValue ?: '<span style="color:red">NULL</span>') . "</td>";
    echo "<td>" . ($configValue ?: '<span style="color:red">NULL</span>') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Raw \$_ENV Dump</h2>";
echo "<pre style='max-height: 300px; overflow: auto; font-size: 11px;'>";
print_r($_ENV);
echo "</pre>";

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
