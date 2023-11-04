<?php

namespace Minigyima\Warden\Providers;

use Closure;
use Illuminate\Foundation\Console\AboutCommand;
use Minigyima\Warden\Services\CacheEntryContainer;
use Minigyima\Warden\Services\Warden;
use Illuminate\Support\ServiceProvider;

/**
 * The service provider for loading Warden as a singleton
 * @package Warden
 */
class WardenServiceProvider extends ServiceProvider
{
    public static Closure|null $authorizable_resolver = null;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/warden.php', 'warden');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');


        $this->publishes([
            __DIR__ . '/../Config/warden.php' => config_path('warden.php'),
            __DIR__ . '/../Permissions' => app_path('Permissions'),
        ]);

        $this->commands([
            \Minigyima\Warden\Commands\FlushWardenCache::class,
            \Minigyima\Warden\Commands\GeneratePermissionCache::class,
        ]);

        AboutCommand::add('Warden', fn () => ['Version' => '1.0.0']);
    }

    /**
     * Register Warden as a singleton, and the CacheEntryContainer as a scoped singleton
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(Warden::class, fn () => new Warden(self::$authorizable_resolver ?? fn () => auth()->user()));

        $this->app->scoped(CacheEntryContainer::class, fn ($app) => new CacheEntryContainer($app[Warden::class]));
    }
}
