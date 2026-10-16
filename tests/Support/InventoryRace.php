<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class InventoryRace
{
    /** Cho hai process đọc cùng tồn kho rồi đồng thời gọi ProductSize::decreaseQuantity(). */
    public static function run(int $stock, int $first, int $second): array
    {
        $prefix = storage_path('framework/inventory-race-'.Str::uuid());
        $databasePath = $prefix.'.sqlite';
        $processes = [];

        try {
            file_put_contents($databasePath, '');
            Config::set('database.connections.inventory_race', array_replace(
                Config::get('database.connections.sqlite'),
                ['database' => $databasePath, 'busy_timeout' => 5000, 'journal_mode' => 'WAL']
            ));
            Schema::connection('inventory_race')->create('product_sizes', function (Blueprint $table) {
                $table->id();
                $table->integer('quantity');
                $table->timestamps();
            });
            DB::connection('inventory_race')->table('product_sizes')->insert(['id' => 1, 'quantity' => $stock]);

            foreach (['a' => $first, 'b' => $second] as $name => $amount) {
                $process = new Process([
                    PHP_BINARY, '-d', 'xdebug.mode=off', __DIR__.'/InventoryRaceWorker.php',
                    $databasePath, $prefix, $name, (string) $amount,
                ], dirname(__DIR__, 2));
                $process->start();
                $processes[$name] = $process;
            }

            $deadline = microtime(true) + 20;
            while (! is_file($prefix.'.a.ready') || ! is_file($prefix.'.b.ready')) {
                clearstatcache();
                if (microtime(true) >= $deadline || ! $processes['a']->isRunning() || ! $processes['b']->isRunning()) {
                    throw new RuntimeException('Hai process không đọc được tồn kho trước thời hạn: '
                        .implode(' | ', array_map(fn (Process $process) => $process->getErrorOutput(), $processes)));
                }
                usleep(10_000);
            }

            file_put_contents($prefix.'.release', 'go');
            $results = [];
            foreach ($processes as $name => $process) {
                $process->wait();
                if (! $process->isSuccessful() || ! is_file($prefix.'.'.$name.'.result')) {
                    throw new RuntimeException('Process '.$name.' cập nhật thất bại: '.$process->getErrorOutput());
                }
                $results[$name] = file_get_contents($prefix.'.'.$name.'.result');
            }

            $remaining = DB::connection('inventory_race')->table('product_sizes')->value('quantity');

            return [$results, $remaining];
        } finally {
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    $process->stop(1);
                }
            }
            DB::disconnect('inventory_race');
            foreach (['.a.ready', '.b.ready', '.a.result', '.b.result', '.release', '.sqlite', '.sqlite-wal', '.sqlite-shm'] as $suffix) {
                @unlink($prefix.$suffix);
            }
        }
    }
}
