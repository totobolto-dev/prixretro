<?php
/**
 * Write production .env file
 * RUN ONCE THEN DELETE!
 */

if (!isset($_GET['confirm'])) {
    die('Add ?confirm=1 to URL to write .env file');
}

$envContent = <<<'ENV'
APP_NAME=PrixRetro
APP_ENV=production
APP_KEY=base64:vfnIP7UKm05SafEly1W+yD7jQTmXj7SIHJXZg00cJQA=
APP_DEBUG=false
APP_URL=https://www.prixretro.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=ba2247864-001.eu.clouddb.ovh.net
DB_PORT=35831
DB_DATABASE=prixretro
DB_USERNAME=prixretro_user
DB_PASSWORD=f5bxVvfQUvkapKgNtjy5

BROADCAST_DRIVER=log
CACHE_DRIVER=file
CACHE_PATH=/home/pwagrad/prixretro/storage/framework/cache
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

VIEW_COMPILED_PATH=/home/pwagrad/prixretro/storage/framework/views

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
ENV;

$envPath = dirname(__DIR__) . '/.env';

$result = file_put_contents($envPath, $envContent);

if ($result !== false) {
    echo "<h1 style='color: green;'>✅ SUCCESS!</h1>";
    echo "<p>.env file written to: {$envPath}</p>";
    echo "<p>File size: {$result} bytes</p>";
    echo "<hr>";
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='/admin'>Try logging in to admin</a></li>";
    echo "<li>Delete all these debug files:</li>";
    echo "<ul>";
    echo "<li>public/write-env.php (THIS FILE!)</li>";
    echo "<li>public/check-env.php</li>";
    echo "<li>public/view-logs.php</li>";
    echo "<li>public/test-admin-access.php</li>";
    echo "<li>public/fix-permissions.php</li>";
    echo "<li>public/check-assets.php</li>";
    echo "<li>public/check-login.php</li>";
    echo "</ul>";
    echo "</ol>";
} else {
    echo "<h1 style='color: red;'>❌ FAILED!</h1>";
    echo "<p>Could not write to: {$envPath}</p>";
    echo "<p>Check file permissions</p>";
}
