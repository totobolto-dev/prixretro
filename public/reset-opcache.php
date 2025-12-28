<?php
if (!isset($_GET['reset'])) {
    die('Add ?reset=1 to URL');
}

echo "<h1>Resetting OPcache...</h1>";

if (!function_exists('opcache_reset')) {
    echo "<p style='color: red;'>❌ opcache_reset() function not available</p>";
    echo "<p>Contact OVH support to reload PHP-FPM</p>";
    exit;
}

$result = opcache_reset();

if ($result) {
    echo "<p style='color: green; font-size: 20px;'>✅ OPcache RESET SUCCESSFUL!</p>";
    echo "<hr>";
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='/check-env.php?allow=1'>Check if .env loads now</a></li>";
    echo "<li>If still not working, check <a href='/check-opcache.php?run=1'>OPcache status again</a></li>";
    echo "<li><a href='/admin'>Try logging into admin</a></li>";
    echo "</ol>";
} else {
    echo "<p style='color: red;'>❌ opcache_reset() returned FALSE</p>";
    echo "<p>You may need to:</p>";
    echo "<ul>";
    echo "<li>Contact OVH support to reload PHP-FPM</li>";
    echo "<li>Or wait for OPcache to expire naturally (usually 1-5 minutes)</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE THIS FILE!</strong></p>";
