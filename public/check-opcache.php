<?php
if (!isset($_GET['run'])) {
    die('Add ?run=1 to URL');
}

echo "<h1>OPcache Status</h1>";

if (!function_exists('opcache_get_status')) {
    echo "<p style='color: red;'>❌ OPcache extension not loaded</p>";
    exit;
}

$status = opcache_get_status();

if ($status === false) {
    echo "<p style='color: orange;'>⚠️ OPcache is installed but disabled</p>";
    exit;
}

echo "<h2>OPcache Enabled</h2>";
echo "<p style='color: green;'>✅ OPcache is ACTIVE</p>";
echo "<p><strong>This is likely why .env isn't loading!</strong></p>";

echo "<h2>Configuration</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Enabled</td><td>" . ($status['opcache_enabled'] ? 'YES' : 'NO') . "</td></tr>";
echo "<tr><td>Cache Full</td><td>" . ($status['cache_full'] ? 'YES' : 'NO') . "</td></tr>";
echo "<tr><td>Cached Scripts</td><td>" . $status['opcache_statistics']['num_cached_scripts'] . "</td></tr>";
echo "<tr><td>Cached Keys</td><td>" . $status['opcache_statistics']['num_cached_keys'] . "</td></tr>";
echo "<tr><td>Max Cached Keys</td><td>" . $status['opcache_statistics']['max_cached_keys'] . "</td></tr>";
echo "<tr><td>Hits</td><td>" . $status['opcache_statistics']['hits'] . "</td></tr>";
echo "<tr><td>Misses</td><td>" . $status['opcache_statistics']['misses'] . "</td></tr>";
echo "<tr><td>Memory Used</td><td>" . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB</td></tr>";
echo "<tr><td>Memory Free</td><td>" . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . " MB</td></tr>";
echo "</table>";

echo "<h2>Cached Files (first 20)</h2>";
if (isset($status['scripts'])) {
    echo "<ul style='max-height: 400px; overflow: auto; font-size: 11px;'>";
    $count = 0;
    foreach ($status['scripts'] as $file => $details) {
        if ($count++ >= 20) break;
        echo "<li>" . htmlspecialchars($file);
        if (strpos($file, 'bootstrap/cache/config.php') !== false) {
            echo " <strong style='color: red;'>← CACHED CONFIG FOUND!</strong>";
        }
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Script list not available</p>";
}

echo "<hr>";
echo "<h2>Solution</h2>";
echo "<p><a href='reset-opcache.php?reset=1' style='color: red; font-weight: bold;'>→ Reset OPcache Now</a></p>";
echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
