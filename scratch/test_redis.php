<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Redis;

echo "=== REDIS DIAGNOSTIC TEST ===\n";

// 1. Check PHP Extension
if (extension_loaded('redis')) {
    echo "✔ PHP Extension 'redis' (phpredis) is LOADED.\n";
} else {
    echo "✖ PHP Extension 'redis' is NOT loaded.\n";
}

// 2. Try Redis Server Connection
try {
    $ping = Redis::ping();
    echo "✔ Redis Connection SUCCESSFUL! Ping response: " . (is_string($ping) ? $ping : 'PONG') . "\n";

    // Test Write & Read
    Redis::set('test_cpanel_key', 'Hello Redis from cPanel - ' . date('Y-m-d H:i:s'));
    $val = Redis::get('test_cpanel_key');
    echo "✔ Redis Write/Read SUCCESSFUL! Retrieved value: '$val'\n";
} catch (\Throwable $e) {
    echo "✖ Redis Server Connection FAILED!\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "\nTip: If Redis daemon is not running on 127.0.0.1:6379, check if your cPanel host provides a custom Redis host/port or socket path in your cPanel dashboard.\n";
}
