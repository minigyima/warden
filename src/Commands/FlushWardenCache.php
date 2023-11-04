<?php

namespace Minigyima\Warden\Commands;

use Illuminate\Console\Command;
use Minigyima\Warden\Cache\ResolvesCacheDriver;

class FlushWardenCache extends Command
{
    use ResolvesCacheDriver;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warden:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flushes the configured CacheDriver for Warden';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $driver = self::newWarmCacheDriver();
        $driver->flush();

        $this->info('Cache flushed');

        return Command::SUCCESS;
    }
}
