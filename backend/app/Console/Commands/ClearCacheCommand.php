<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all {--type=all : Type of cache to clear (all, posts, profiles)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'posts':
                $this->clearPostsCache();
                break;
            case 'profiles':
                $this->clearProfilesCache();
                break;
            case 'all':
            default:
                $this->clearAllCache();
                break;
        }

        $this->info('Cache cleared successfully!');
    }

    /**
     * Clear all application cache.
     */
    private function clearAllCache(): void
    {
        Cache::flush();
        $this->info('All cache cleared.');
    }

    /**
     * Clear posts cache.
     */
    private function clearPostsCache(): void
    {
        Cache::forget('posts.all');
        $this->info('Posts cache cleared.');
    }

    /**
     * Clear user profiles cache.
     */
    private function clearProfilesCache(): void
    {
        // ユーザープロフィールのキャッシュパターンをクリア
        $keys = Cache::get('user.profile.*');
        if ($keys) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
        $this->info('User profiles cache cleared.');
    }
}
