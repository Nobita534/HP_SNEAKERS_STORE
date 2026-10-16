<?php

use App\Models\ProductSize;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;

require dirname(__DIR__, 2).'/vendor/autoload.php';

// Process riêng đọc cùng một bản ghi trước khi cả hai được phép cập nhật.
[$script, $databasePath, $signalPath, $name, $amount] = $argv;
$_SERVER['APP_ENV'] = 'testing';
$_ENV['APP_ENV'] = 'testing';
putenv('APP_ENV=testing');

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
Config::set('database.connections.inventory_race', array_replace(
    Config::get('database.connections.sqlite'),
    ['database' => $databasePath, 'busy_timeout' => 5000, 'journal_mode' => 'WAL']
));

$variant = (new ProductSize)->setConnection('inventory_race')->newQuery()->findOrFail(1);
file_put_contents($signalPath.'.'.$name.'.ready', 'ready');

$deadline = microtime(true) + 20;
while (! is_file($signalPath.'.release')) {
    clearstatcache();
    if (microtime(true) >= $deadline) {
        fwrite(STDERR, 'Hết thời gian chờ bắt đầu phép thử.');
        exit(2);
    }
    usleep(10_000);
}

try {
    $variant->decreaseQuantity((int) $amount);
    $result = 'success';
} catch (RuntimeException $exception) {
    $result = 'rejected';
}

file_put_contents($signalPath.'.'.$name.'.result', $result);
