<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>Direct Admin Test</h1>";

$basePath = dirname(__DIR__);

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

echo "<h2>Test 1: Request to /admin</h2>";

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/admin', 'GET', [], [], [], [
        'HTTPS' => 'on',
        'HTTP_HOST' => 'www.prixretro.com',
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_HOST' => 'www.prixretro.com',
    ]);

    $response = $kernel->handle($request);

    echo "<p><strong>Status:</strong> {$response->getStatusCode()}</p>";

    if ($response->getStatusCode() === 302) {
        $location = $response->headers->get('Location');
        echo "<p><strong>Redirect Location:</strong> {$location}</p>";

        if (strpos($location, 'localhost') !== false) {
            echo "<p style='color: red;'>❌ Still redirecting to localhost!</p>";
        } else {
            echo "<p style='color: green;'>✅ Redirect looks correct!</p>";
        }
    } elseif ($response->getStatusCode() === 403) {
        echo "<p style='color: red;'>❌ 403 Forbidden</p>";
        echo "<pre style='max-height: 300px; overflow: auto;'>";
        echo htmlspecialchars(substr($response->getContent(), 0, 2000));
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: {$e->getMessage()}</p>";
    echo "<pre style='font-size: 11px; max-height: 300px; overflow: auto;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h2>Test 2: Request to /admin/login</h2>";

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/admin/login', 'GET', [], [], [], [
        'HTTPS' => 'on',
        'HTTP_HOST' => 'www.prixretro.com',
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_HOST' => 'www.prixretro.com',
    ]);

    $response = $kernel->handle($request);

    echo "<p><strong>Status:</strong> {$response->getStatusCode()}</p>";

    if ($response->getStatusCode() === 200) {
        echo "<p style='color: green;'>✅ Login page loads!</p>";
    } elseif ($response->getStatusCode() === 403) {
        echo "<p style='color: red;'>❌ 403 Forbidden</p>";
        echo "<pre style='max-height: 300px; overflow: auto;'>";
        echo htmlspecialchars(substr($response->getContent(), 0, 2000));
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: {$e->getMessage()}</p>";
    echo "<pre style='font-size: 11px; max-height: 300px; overflow: auto;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h2>Latest Error Logs</h2>";

$logFile = $basePath . '/storage/logs/laravel.log';

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);

    // Get only today's errors
    $today = date('Y-m-d');
    $lines = explode("\n", $logs);
    $todayErrors = [];

    foreach ($lines as $line) {
        if (strpos($line, $today) !== false || !empty($todayErrors)) {
            $todayErrors[] = $line;
        }
    }

    $recent = array_slice($todayErrors, -100);

    echo "<pre style='max-height: 500px; overflow: auto; background: #f0f0f0; padding: 10px; font-size: 11px;'>";
    echo htmlspecialchars(implode("\n", $recent));
    echo "</pre>";
} else {
    echo "<p>No log file found</p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
