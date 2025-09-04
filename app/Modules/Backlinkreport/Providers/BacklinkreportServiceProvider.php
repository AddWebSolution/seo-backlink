<?php

namespace App\Modules\Backlinkreport\Providers;

use Illuminate\Support\ServiceProvider;

class BacklinkreportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(base_path('app/Modules/Backlinkreport/Database/migrations'));
        $this->loadRoutesFrom(base_path('app/Modules/Backlinkreport/Http/backlinkreport.api.php'));
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
        {
            //        $this->publishes([
            //            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php'),
            //        ], 'config');
            //        $this->mergeConfigFrom(
            //            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
            //        );
        }
}
