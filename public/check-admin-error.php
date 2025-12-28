<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Testing /admin Route</h1>";

$basePath = dirname(__DIR__);

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

echo "<h2>Creating Request to /admin</h2>";

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/admin', 'GET');

    echo "<p>✅ Request created</p>";
    echo "<p><strong>URL:</strong> /admin</p>";
    echo "<p><strong>Method:</strong> GET</p>";

    echo "<h2>Handling Request...</h2>";

    $response = $kernel->handle($request);

    echo "<p><strong>Response Status:</strong> {$response->getStatusCode()}</p>";
    echo "<p><strong>Response Headers:</strong></p>";
    echo "<pre>";
    foreach ($response->headers->all() as $key => $values) {
        echo htmlspecialchars($key) . ": " . htmlspecialchars(implode(', ', $values)) . "\n";
    }
    echo "</pre>";

    if ($response->getStatusCode() === 403) {
        echo "<h2 style='color: red;'>403 Forbidden Response</h2>";
        echo "<p><strong>Response Content:</strong></p>";
        echo "<pre style='max-height: 400px; overflow: auto; background: #f0f0f0; padding: 10px;'>";
        echo htmlspecialchars(substr($response->getContent(), 0, 5000));
        echo "</pre>";
    } elseif ($response->getStatusCode() === 302) {
        echo "<h2 style='color: green;'>302 Redirect (Good!)</h2>";
        echo "<p><strong>Redirecting to:</strong> {$response->headers->get('Location')}</p>";
    } elseif ($response->getStatusCode() === 200) {
        echo "<h2 style='color: green;'>200 OK (Good!)</h2>";
    } else {
        echo "<h2>Unexpected Status Code</h2>";
        echo "<pre style='max-height: 400px; overflow: auto;'>";
        echo htmlspecialchars(substr($response->getContent(), 0, 2000));
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Exception Caught!</h2>";
    echo "<p><strong>Type:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> {$e->getFile()}:{$e->getLine()}</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='max-height: 400px; overflow: auto; font-size: 11px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h2>Recent Laravel Logs</h2>";

$logFile = $basePath . '/storage/logs/laravel.log';

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $lines = explode("\n", $logs);
    $recent = array_slice($lines, -50);

    echo "<pre style='max-height: 500px; overflow: auto; background: #f0f0f0; padding: 10px; font-size: 11px;'>";
    echo htmlspecialchars(implode("\n", $recent));
    echo "</pre>";
} else {
    echo "<p>No log file found</p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
