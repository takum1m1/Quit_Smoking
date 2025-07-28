<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show cache status and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Cache Status ===');

        // Redis接続確認
        try {
            $redis = Redis::connection();
            $redis->ping();
            $this->info('✅ Redis connection: OK');

            // Redis統計情報
            $info = $redis->info();
            $keys = $info['db0']['keys'] ?? 0;
            $memory = $info['used_memory'] ?? 0;
            $this->info("📊 Redis keys: {$keys}");
            $this->info("💾 Memory used: " . $this->formatBytes($memory));

        } catch (\Exception $e) {
            $this->error('❌ Redis connection failed: ' . $e->getMessage());
            return 1;
        }

        // アプリケーションキャッシュの状態
        $this->info("\n=== Application Cache ===");

        // 投稿キャッシュ
        $postsCache = Cache::has('posts.all');
        $this->info($postsCache ? '✅ Posts cache: Available' : '❌ Posts cache: Not available');

        // キャッシュドライバー確認
        $driver = config('cache.default');
        $this->info("🔧 Cache driver: {$driver}");

        // キャッシュ設定確認
        $this->info("\n=== Cache Configuration ===");
        $this->info("Default store: " . config('cache.default'));
        $this->info("Redis host: " . config('database.redis.default.host'));
        $this->info("Redis port: " . config('database.redis.default.port'));
        $this->info("Cache prefix: " . config('cache.prefix'));

        return 0;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
