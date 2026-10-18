<?php

// Tạo database riêng cho test; tuyệt đối không chạy RefreshDatabase trên dữ liệu cửa hàng.
require __DIR__.'/../../vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if ($app->configurationIsCached()) {
    throw new RuntimeException('Hãy chạy php artisan config:clear trước khi test để kết nối database tạm được áp dụng.');
}

$connection = DB::connection('pgsql');
$database = 'hp_search_test_'.bin2hex(random_bytes(6));
$configPath = tempnam(base_path(), '.phpunit-search-');
$created = false;
$exitCode = 1;

try {
    $connection->statement('CREATE DATABASE "'.$database.'"');
    $created = true;

    $xml = new DOMDocument;
    $xml->load(base_path('phpunit.xml'));
    foreach (['env', 'server'] as $tag) {
        foreach ($xml->getElementsByTagName($tag) as $node) {
            $name = $node->getAttribute('name');
            if ($name === 'DB_CONNECTION' || $name === 'DB_DATABASE') {
                $node->setAttribute('value', $name === 'DB_CONNECTION' ? 'pgsql' : $database);
                $node->setAttribute('force', 'true');
            }
        }
    }
    $xml->save($configPath);

    // Truyền thông tin kết nối qua môi trường, không ghi mật khẩu vào file test.
    $environment = [];
    foreach (['host', 'port', 'username', 'password'] as $key) {
        $environment['DB_'.strtoupper($key)] = (string) $connection->getConfig($key);
    }
    $process = new Process([
        PHP_BINARY, '-d', 'xdebug.mode=off', 'vendor/bin/pest',
        '--configuration', $configPath, 'tests/Feature/ProductSearchTest.php', '--compact',
    ], base_path(), $environment);
    $process->setTimeout(180);
    $exitCode = $process->run(fn ($type, $output) => print $output);
} finally {
    if (is_file($configPath)) {
        unlink($configPath);
    }
    if ($created) {
        $connection->select('SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = ? AND pid <> pg_backend_pid()', [$database]);
        $connection->statement('DROP DATABASE "'.$database.'"');
    }
}

exit($exitCode);
