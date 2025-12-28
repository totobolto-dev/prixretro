<?php
echo "<h1>PHP Function Check</h1>";

// Check if putenv is disabled
$disabled = ini_get('disable_functions');
echo "<h2>Disabled Functions</h2>";
echo "<pre>" . ($disabled ?: 'None') . "</pre>";

echo "<h2>putenv() Test</h2>";
if (function_exists('putenv')) {
    echo "<p style='color: green;'>✅ putenv() function exists</p>";
    
    // Try to actually use it
    $result = @putenv('TEST_VAR=hello');
    if ($result) {
        echo "<p style='color: green;'>✅ putenv() works!</p>";
        echo "<p>getenv('TEST_VAR') = " . getenv('TEST_VAR') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ putenv() exists but FAILS (likely disabled in php.ini)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ putenv() function does NOT exist</p>";
}

echo "<h2>variables_order</h2>";
echo "<p>" . ini_get('variables_order') . "</p>";
echo "<p>Should contain 'E' for \$_ENV and 'S' for \$_SERVER</p>";

echo "<hr>";
echo "<p><strong style='color: red;'>DELETE THIS FILE!</strong></p>";
