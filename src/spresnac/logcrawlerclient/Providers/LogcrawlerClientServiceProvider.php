<?php

namespace spresnac\logcrawlerclient\Providers;

use App\Console\Commands\Selfdiagnostic;
use Illuminate\Support\ServiceProvider;

class LogcrawlerClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/logcrawler.php' => base_path('config/logcrawler.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Selfdiagnostic::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/logcrawler.php', 'logcrawler');
    }
}
